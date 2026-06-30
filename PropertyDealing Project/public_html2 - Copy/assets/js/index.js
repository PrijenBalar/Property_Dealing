$(document).ready(function() {
  // Debounced area search (perf improvement)
  let areaSearchTimeout;
  $('#areaSearch').on('input', function() {
    clearTimeout(areaSearchTimeout);
    areaSearchTimeout = setTimeout(() => {
      const value = $(this).val().toLowerCase();
      $('#areaList .area-item').each(function() {
        $(this).toggle($(this).text().toLowerCase().includes(value));
      });
    }, 300); // 300ms debounce
  });



  // Lazy load images for perf
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove('lazy');
          imageObserver.unobserve(img);
        }
      });
    });
    $('.property-image').each(function() {
      $(this).attr('data-src', $(this).attr('src')).removeAttr('src').addClass('lazy');
      imageObserver.observe(this);
    });
  }

  // Smooth scroll restore
  $(window).on('load', function() {
    const savedY = sessionStorage.getItem("indexScrollY");
    if (savedY) {
      $('html, body').animate({scrollTop: parseInt(savedY)}, 0);
      sessionStorage.removeItem("indexScrollY");
    }
  });

  // Filter toggle improvements
window.toggleFilter = function(id) {
    const el = document.getElementById(id);
    const bsCollapse = new bootstrap.Collapse(el, {toggle: true});
  };
  
  // Close filters on outside click
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#mainFilterBar, #filterToggle').length) {
      $('#mainFilterBar').collapse('hide');
    }
  });

  // Global Back Button logic (used in property details, contact)
  const backBtn = document.getElementById("backBtn");
  if (backBtn) {
    backBtn.addEventListener("click", function () {
      if (window.history.length > 1) {
        history.back();
      } else {
        window.location.href = "index.php";
      }
    });
  }

  /* =========================
     DYNAMIC FILTER VISIBILITY
  ========================= */
  function updateFilterVisibility() {
    const isCommChecked = $('.commercial-type:checked').length > 0;
    const isResChecked = $('.residential-type:checked').length > 0;

    const bhkCol = $('#bhkFilterCol');
    const resAvail = $('#resAvailItems');
    const commAvail = $('#commAvailItems');
    const availLabel = $('#availFilterLabel');

    if (isCommChecked && !isResChecked) {
      // ONLY Commercial selected
      bhkCol.hide();
      resAvail.hide();
      commAvail.show();
      availLabel.text("Property Type"); // Rename to be more descriptive
    } 
    else if (isCommChecked && isResChecked) {
      // BOTH selected
      bhkCol.show();
      resAvail.show();
      commAvail.show();
      availLabel.text("Sub-Type");
    }
    else {
      // ONLY Residential or NONE selected
      bhkCol.show();
      resAvail.show();
      commAvail.hide();
      availLabel.text("Availability");
    }
  }

  // Listen for changes
  $('.type-checkbox').on('change', updateFilterVisibility);

  // Run on load
  updateFilterVisibility();

  // Copy Share Link Utility
  $('#copyShareLink').on('click', function() {
    const url = $(this).data('url');
    const $btn = $(this);
    const originalHtml = $btn.html();

    function updateBtn() {
      $btn.html('<i class="bi bi-check2"></i> Copied!').addClass('btn-primary').removeClass('btn-outline-primary');
      setTimeout(() => {
        $btn.html(originalHtml).addClass('btn-outline-primary').removeClass('btn-primary');
      }, 2000);
    }

    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(updateBtn).catch(err => {
        // Fallback to execCommand
        copyFallback(url);
      });
    } else {
      copyFallback(url);
    }

    function copyFallback(text) {
      const textarea = document.createElement("textarea");
      textarea.value = text;
      document.body.appendChild(textarea);
      textarea.select();
      try {
        document.execCommand("copy");
        updateBtn();
      } catch (err) {
        console.error("Fallback copy failed", err);
      }
      document.body.removeChild(textarea);
    }
  });

});
