$(document).ready(function(){
  toastr.options = {
    "positionClass": "toast-top-right",
    "timeOut": "2000"
  };

  // --- Initialize Bootstrap Tooltips ---
  function initTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  }
  initTooltips();

  $("#pendingCard").click(function(){
    $("#pendingTableSection").slideToggle(300, function() {
      if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#propertyTable')) {
        $('#propertyTable').DataTable().columns.adjust().draw();
      }
    });
  });

  // Dashboard Table Init (with safety check)
  if ($('#propertyTable').length && typeof $.fn.DataTable !== 'undefined') {
    $('#propertyTable').DataTable({
      order: [[0, 'desc']], // Latest property first
      dom: 'Bfrtip',
      pagingType: 'simple_numbers',
      buttons: [
        {
          extend: 'excel',
          text: '<i class="fa-solid fa-file-excel"></i>',
          className: 'btn btn-excel'
        },
        {
          extend: 'pdf',
          text: '<i class="fa-solid fa-file-pdf"></i>',
          className: 'btn btn-pdf'
        }
      ],
      pageLength: 5,
      language: {
        paginate: { previous: "Previous", next: "Next" },
        search: "",
        searchPlaceholder: "Search properties..."
      }
    });
  }

  // Manage Property Table Init (with safety check)
  if ($('#managePropertyTable').length && typeof $.fn.DataTable !== 'undefined') {
    $('#managePropertyTable').DataTable({
      order: [[0, 'desc']], // Latest property first
      dom: 'frtip', // Simplified DOM (no 'B')
      pagingType: 'simple_numbers',
      pageLength: 10, // Default to 10 for management view
      language: {
        paginate: { previous: "Previous", next: "Next" },
        search: "",
        searchPlaceholder: "Search properties..."
      }
    });
  }

  // Approve / Toggle Status AJAX (Unified Handler)
  $(document).on("click", ".toggleStatusBtn, .approveBtn", function(e) {
    e.preventDefault();
    let btn = $(this);
    let id = btn.data("id");
    let isDashboard = btn.hasClass('approveBtn');
    let badge = $("#status_" + id);
    let icon = btn.find("i");
    let row = $("#row_" + id);

    btn.prop("disabled", true).css('opacity', '0.5');

    $.ajax({
      url: "ajax-status.php",
      type: "POST",
      data: { id: id },
      dataType: "json",
      success: function(res) {
        if (res.status === 'success') {
          let newStatus = res.new_status;
          
          if (isDashboard) {
            toastr.success("Property Approved Successfully!");
            row.fadeOut(400, function() { 
              row.remove(); 
              // Update dashboard count
              let countEl = $("#pendingCount");
              if (countEl.length) {
                  let count = parseInt(countEl.text());
                  countEl.text(count > 0 ? count - 1 : 0);
              }
            });
          } else {
            // Manage Property logic
            badge.text(newStatus).removeClass("bg-success bg-warning bg-success-subtle text-success border-success-subtle bg-warning-subtle text-warning border-warning-subtle text-capitalize");
            
            if (newStatus === 'approved') {
              badge.addClass("bg-success-subtle text-success border-success-subtle text-capitalize");
              btn.removeClass("btn-status-approve").addClass("btn-status-pending").attr("title", "Make Pending");
              icon.removeClass("fa-check fa-clock").addClass("fa-clock");
            } else {
              badge.addClass("bg-warning-subtle text-warning border-warning-subtle text-capitalize");
              btn.removeClass("btn-status-pending").addClass("btn-status-approve").attr("title", "Approve");
              icon.removeClass("fa-check fa-clock").addClass("fa-check");
            }
            toastr.success(res.message);
            // Re-init tooltips after status change
            initTooltips();
          }
        } else {
          toastr.error(res.message);
        }
        btn.prop("disabled", false).css('opacity', '1');
      },
      error: function() {
        toastr.error("Server Error: Status update failed");
        btn.prop("disabled", false).css('opacity', '1');
      }
    });
  });

  // Delete Property with SweetAlert
  $(document).on("click", ".deletePropertyBtn", function() {
    let id = $(this).data("id");
    let row = $("#row_" + id);

    Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete the property and all its media!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "ajax-delete.php",
          type: "POST",
          data: { id: id },
          dataType: "json",
          success: function(res) {
            if (res.status === 'success') {
              row.fadeOut(400, function() {
                row.remove();
              });
              Swal.fire('Deleted!', res.message, 'success');
            } else {
              Swal.fire('Error!', res.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error!', 'System error occurred.', 'error');
          }
        });
      }
    });
  });
  // Delete Specific Media (Edit Form)
  $(document).on("click", ".deleteMediaBtn", function() {
    let btn = $(this);
    let id = btn.data("id");
    let type = btn.data("type");
    let tile = $("#media_" + (type === "image" ? "img_" : "vid_") + id);

    Swal.fire({
      title: 'Delete this ' + type + '?',
      text: "This will permanently remove the file from the server!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "ajax-delete-media.php",
          type: "POST",
          data: { id: id, type: type },
          dataType: "json",
          success: function(res) {
            if (res.status === 'success') {
              tile.fadeOut(400, function() {
                tile.remove();
              });
              toastr.success(res.message);
            } else {
              toastr.error(res.message);
            }
          },
          error: function() {
            toastr.error("Network Error");
          }
        });
      }
    });
  });

  // Sidebar Toggle (Mobile)
  $("#sidebarToggle").click(function() {
    $(".sidebar").toggleClass("active");
    $("#sidebarOverlay").toggleClass("show");
  });

  $("#sidebarOverlay").click(function() {
    $(".sidebar").removeClass("active");
    $(this).removeClass("show");
  });

  // Auto-close sidebar on link click (mobile)
  $(".sidebar a").click(function() {
    if ($(window).width() < 992) {
      $(".sidebar").removeClass("active");
      $("#sidebarOverlay").removeClass("show");
    }
  });

  // ==========================================
  // MEDIA MODAL LOGIC (DASHBOARD)
  // ==========================================
  const mediaModal = $('#mediaModal');
  const mediaGallery = $('#mediaGallery');
  const mediaLoading = $('#mediaLoading');

  $(document).on('click', '.view-media-btn', function() {
    const propertyId = $(this).data('id');
    
    // Reset Modal
    mediaGallery.html('');
    mediaLoading.show();
    mediaModal.modal('show');

    $.ajax({
      url: 'ajax-get-media.php',
      type: 'POST',
      data: { id: propertyId },
      dataType: 'json',
      success: function(res) {
        mediaLoading.hide();
        if (res.status === 'success') {
          let html = '';
          
          // Add Images
          if (res.images && res.images.length > 0) {
            res.images.forEach(img => {
              html += `
                <div class="col-4 col-md-3">
                  <a href="../uploads/${img}" target="_blank" class="d-block media-link">
                    <img src="../uploads/${img}" class="img-fluid rounded-3 shadow-sm border border-light" style="height: 100px; width: 100%; object-fit: cover;">
                  </a>
                </div>`;
            });
          }

          // Add Videos
          if (res.videos && res.videos.length > 0) {
            res.videos.forEach(vid => {
              html += `
                <div class="col-4 col-md-3">
                  <a href="../uploads/${vid}" target="_blank" class="d-block media-link">
                    <div class="bg-dark rounded-3 d-flex align-items-center justify-content-center" style="height: 100px;">
                      <i class="fas fa-play text-white fs-4"></i>
                    </div>
                  </a>
                </div>`;
            });
          }

          if (!html) {
            html = '<div class="col-12 text-center py-4 text-muted">No media found for this property.</div>';
          }

          mediaGallery.html(html);
        } else {
          mediaGallery.html('<div class="col-12 text-danger text-center py-4">' + res.message + '</div>');
        }
      },
      error: function() {
        mediaLoading.hide();
        mediaGallery.html('<div class="col-12 text-danger text-center py-4">Error loading media. Please try again.</div>');
      }
    });
  });

  // ==========================================
  // ADD PROPERTY PAGE VALIDATION & LOGIC
  // ==========================================
  const addPropertyForm = $(".needs-validation");
  
  if (addPropertyForm.length) {
    const mobileInput = $("#owner_mobile");
    const warningBox = $("#mobileWarning");
    const propertyType = $("#propertyType");
    const bhkWrapper = $("#bhkWrapper");
    const availabilityField = $("#availabilityField");

    // --- Validation Helper ---
    function validateField(input, condition, message) {
      if (condition) {
        input.addClass("is-invalid").removeClass("is-valid");
        if (message) toastr.warning(message);
        return false;
      } else {
        input.removeClass("is-invalid").addClass("is-valid");
        return true;
      }
    }

    // --- Real-time Validation ---
    
    // 1. Mobile Number (Numbers only)
    mobileInput.on("input", function() {
      let val = $(this).val();
      $(this).val(val.replace(/\D/g, '')); // Enforce numeric
    });

    // 2. Clear errors when user types or changes value
    $("textarea, input, select").on("input change", function() {
      if ($(this).val().trim() !== "") {
        $(this).removeClass("is-invalid").addClass("is-valid");
      }
    });

    // --- Duplicate Mobile Check (AJAX) ---
    mobileInput.on("blur", function() {
      const mobile = $(this).val().trim();

      if (mobile.length !== 10) {
        warningBox.hide();
        return;
      }

      $.post("check-mobile.php", { mobile: mobile }, function(data) {
        if (data.trim() === "exists") {
          warningBox.show();
          mobileInput.addClass("is-invalid").removeClass("is-valid");
          toastr.error("This mobile number is already registered!");
        } else {
          warningBox.hide();
          mobileInput.removeClass("is-invalid").addClass("is-valid");
        }
      });
    });

    // --- Dynamic Fields Logic ---
    function updatePropertyFields() {
      let value = propertyType.val() ? propertyType.val().toLowerCase() : "";
      const bhkSelect = $("#bhk");

      if (value.includes("commercial")) {
        bhkWrapper.fadeOut(200);
        bhkSelect.removeAttr("required");
        
        availabilityField.html(`
          <option value="">Select Type</option>
          <option>Basement</option><option>Commercial Space</option>
          <option>Pre-Lease Property</option><option>Ware House</option>
          <option>Co Working Space</option><option>Factory</option>
          <option>Restaurant</option><option>Commercial Building</option>
          <option>Godown</option><option>Shed</option>
          <option>Commercial Bungalow</option><option>Industrial Land</option>
          <option>Shop</option><option>Commercial Flat</option>
          <option>Industrial Shed</option><option>Showroom</option>
          <option>Commercial Plot</option><option>Office</option>
          <option>Space</option>
        `);
      } else {
        bhkWrapper.fadeIn(200);
        bhkSelect.attr("required", true);
        
        availabilityField.html(`
          <option value="">Select Preference</option>
          <option>Family</option>
          <option>Bachelor</option>
          <option>Family & Bachelor</option>
        `);
      }
    }

    propertyType.on("change", updatePropertyFields);
    if (propertyType.val()) updatePropertyFields();

    // --- Global Form Submission Validation ---
    addPropertyForm.on("submit", function(e) {
      // Reset previous validation state
      addPropertyForm.find(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
      let isValid = true;
      let firstError = null;

      // Check all required fields
      addPropertyForm.find("[required]").each(function() {
        let val = $(this).val() ? $(this).val().trim() : "";
        if (!val || val === "") {
          isValid = false;
          $(this).addClass("is-invalid");
          if (!firstError) firstError = $(this);
        }
      });

      // Special Check for Mobile length
      if (mobileInput.val().length !== 10) {
        isValid = false;
        mobileInput.addClass("is-invalid");
        if (!firstError) firstError = mobileInput;
      }

      // Check Owner Name pattern
      const nameVal = $("#owner_name").val();
      if (nameVal && !/^[a-zA-Z0-9\s]*$/.test(nameVal)) {
          isValid = false;
          $("#owner_name").addClass("is-invalid");
          if (!firstError) firstError = $("#owner_name");
      }

      if (!isValid) {
        e.preventDefault();
        toastr.error("Please correct the errors in the form.");
        if (firstError) {
          $('html, body').animate({
            scrollTop: firstError.offset().top - 100
          }, 500);
          firstError.focus();
        }
        return false;
      }

      // Show loading state
      const submitBtn = $(this).find('button[type="submit"]');
      submitBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
    });

    // --- File Input Labels ---
    $('input[type="file"]').on('change', function() {
      const files = this.files;
      const labelSpan = $(this).siblings('span');
      
      if (files.length > 0) {
        const fileName = files.length > 1 ? `${files.length} files selected` : files[0].name;
        labelSpan.text(fileName).addClass('text-primary fw-bold');
      } else {
        labelSpan.text("Click to upload").removeClass('text-primary fw-bold');
      }
    });

  }

});
