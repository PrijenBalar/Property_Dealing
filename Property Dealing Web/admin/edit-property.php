<?php include "layout-header.php"; ?>
<?php
include "../includes/config.php";
include "../includes/auth.php";

/* ======================
   PRESERVE PAGE & SEARCH
====================== */
$currentPage   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentSearch = $_GET['search'] ?? '';

$returnQuery = http_build_query([
    'page'   => $currentPage,
    'search'=> $currentSearch
]);

$id = (int)($_GET['id'] ?? 0);
$message = "";

/* ======================
   FETCH PROPERTY
====================== */
$pq = mysqli_query($con, "SELECT * FROM tbl_property WHERE id=$id");
$p  = mysqli_fetch_assoc($pq);

if (!$p) {
    die("Property not found");
}

$areas = [
  "SG Road", "Akhbarnagar", "Ambawadi", "Ambli", "Ashram Road", "Bapunagar", "Bhadaj", "Bhuyangdev", "Bodakdev", "Bopal", "CG Road", "Chanakyapuri", "Chandkheda", "Chandlodia", "Drive In Road", "Ellisbridge", "Gandhinagar", "Ghatlodia", "Ghuma", "Gift City", "Gota", "Gulbai Tekra", "Gurukul", "Hebatpur", "Income Tax", "Iscon Ambli Road", "Jagatpur", "Jivraj Park", "Jodhpur", "Juhapura", "Kudasan", "Law Garden", "Makarba", "Manekbaug", "Manipur", "Memnagar", "Mithakhali", "Motera", "Naroda", "Naranpura", "Navrangpura", "Nehru Nagar", "New CG Road", "New Ranip", "New Wadaj", "Nikol", "Nirnay Nagar", "Ognaj", "Paldi", "Prahlad Nagar", "Ramdevnagar", "Ranip", "Sabarmati", "Sanathal", "Santej", "Sargasan", "Sarkhej", "Satadhar", "Satellite", "Science City", "Shahibaug", "Shastrinagar", "Shela", "Shilaj", "Shivranjani", "Shyamal", "Sindhubhavan Road", "Sola", "South Bopal", "SP Ring Road", "Subhash Bridge", "Surdhara Circle", "Thaltej", "Tragad", "Usmanpura", "Vahelal", "Vaishno Devi", "Vasna", "Vastral", "Vastrapur", "Vejalpur", "Vijay Cross Road", "Wadaj", "Zundal"
];

/* =====================
   HELPER: CONVERT ANY IMAGE TO WEBP
===================== */
function convertToWebp($sourcePath, $destinationPath, $quality = 80) {
    $info = getimagesize($sourcePath);
    if (!$info) return false;
    switch ($info['mime']) {
        case 'image/jpeg': $image = imagecreatefromjpeg($sourcePath); break;
        case 'image/png': $image = imagecreatefrompng($sourcePath); imagepalettetotruecolor($image); imagealphablending($image, true); imagesavealpha($image, true); break;
        case 'image/webp': return move_uploaded_file($sourcePath, $destinationPath);
        default: return false;
    }
    $result = imagewebp($image, $destinationPath, $quality);
    imagedestroy($image);
    return $result;
}

/* ======================
   UPDATE PROPERTY
====================== */
if (isset($_POST['update_property'])) {
    $owner_name   = mysqli_real_escape_string($con, trim($_POST['owner_name']));
    $owner_mobile = mysqli_real_escape_string($con, trim($_POST['owner_mobile']));
    $price        = mysqli_real_escape_string($con, trim($_POST['price']));
    $type         = mysqli_real_escape_string($con, $_POST['property_type']);
    $area         = mysqli_real_escape_string($con, $_POST['area']);
    $address      = mysqli_real_escape_string($con, trim($_POST['address']));
    $bhk          = mysqli_real_escape_string($con, $_POST['bhk'] ?? "");
    $condition    = mysqli_real_escape_string($con, $_POST['property_condition']);
    $availability = mysqli_real_escape_string($con, $_POST['availability']);
    $address_link = mysqli_real_escape_string($con, trim($_POST['address_link']));
    $description  = mysqli_real_escape_string($con, trim($_POST['description']));

    /* ===== VALIDATION ===== */
    if (!preg_match("/^[a-zA-Z0-9 ]+$/", $owner_name)) {
        $message = "Owner name can contain letters, numbers and spaces only";
    } elseif (!preg_match("/^[0-9]{10}$/", $owner_mobile)) {
        $message = "Owner mobile must be 10 digits";
    } else {

        /* ===== UPDATE QUERY ===== */
        mysqli_query($con, "
            UPDATE tbl_property SET
                owner_name='$owner_name',
                owner_mobile='$owner_mobile',
                price='$price',
                type='$type',
                area='$area',
                address='$address',
                bhk='$bhk',
                property_condition='$condition',
                availability='$availability',
                address_link='$address_link',
                description='$description'
            WHERE id=$id
        ");

        /* ===== ADD NEW IMAGES (FORCE WEBP) ===== */
        if (!empty($_FILES['images']['name'][0])) {
            $allowed_img = ['jpg','jpeg','png','webp'];
            foreach ($_FILES['images']['name'] as $k => $name) {
                if ($name) {
                    $tmp = $_FILES['images']['tmp_name'][$k];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed_img)) continue;

                    $img  = uniqid('img_') . '.webp';
                    $dest = "../uploads/" . $img;

                    if (convertToWebp($tmp, $dest, 80)) {
                        mysqli_query($con, "INSERT INTO tbl_property_images(property_id,image) VALUES('$id','$img')");
                    }
                }
            }
        }

        /* ===== ADD NEW VIDEOS ===== */
        if (!empty($_FILES['videos']['name'][0])) {
            foreach ($_FILES['videos']['name'] as $k => $name) {
                if ($name) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $vid = uniqid('vid_') . '.' . $ext;
                    move_uploaded_file($_FILES['videos']['tmp_name'][$k], "../uploads/" . $vid);
                    mysqli_query($con, "INSERT INTO tbl_property_videos(property_id,video) VALUES('$id','$vid')");
                }
            }
        }

        $_SESSION['success'] = "Property updated successfully!";
        header("Location: manage-property.php?$returnQuery");
        exit;
    }
}
?>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="premium-card animate-fade-in shadow-lg">
                <div class="premium-card-header text-center position-relative">
                   
                    <h4><i class="fas fa-edit"></i> Edit Property #<?= $id ?></h4>
                    <p class="mb-0 text-white-50 small">Modify existing property details</p>
                </div>
                
                <div class="card-body p-4 p-md-5 text-dark">
                    <?php if ($message): ?>
                    <script>
                        window.onload = function() {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= addslashes($message) ?>', confirmButtonColor: '#3b82f6' });
                        };
                    </script>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" autocomplete="off" class="needs-validation" novalidate>
                        <input type="hidden" name="update_property" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-custom">Owner Name</label>
                                <div class="form-group-custom">
                                    <input type="text" name="owner_name" class="form-control form-control-custom" value="<?= htmlspecialchars($p['owner_name']) ?>" required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Mobile Number</label>
                                <div class="form-group-custom">
                                    <input type="text" name="owner_mobile" class="form-control form-control-custom" value="<?= htmlspecialchars($p['owner_mobile']) ?>" maxlength="10" required>
                                    <i class="fas fa-phone-alt input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Price</label>
                                <div class="form-group-custom">
                                    <input type="text" name="price" id="price" class="form-control form-control-custom" value="<?= htmlspecialchars($p['price']) ?>" required>
                                    <i class="fas fa-tag input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Property Type</label>
                                <div class="form-group-custom">
                                    <select name="property_type" id="propertyType" class="form-control form-control-custom" required>
                                        <option value="">Select Type</option>
                                        <?php $types=["Residential Rent","Residential Sell","Commercial Rent","Commercial Sell"];
                                        foreach($types as $t){ $sel=($p['type']==$t)?'selected':''; echo "<option $sel>$t</option>"; } ?>
                                    </select>
                                    <i class="fas fa-building input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Area / Location</label>
                                <div class="form-group-custom">
                                    <select name="area" class="form-control form-control-custom" required>
                                        <option value="">Select Area</option>
                                        <?php foreach ($areas as $area) { $sel=($p['area']==$area)?'selected':''; echo "<option value=\"".htmlspecialchars($area)."\" $sel>".htmlspecialchars($area)."</option>"; } ?>
                                    </select>
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6" id="bhkWrapper">
                                <label class="label-custom">BHK / Configuration</label>
                                <div class="form-group-custom">
                                    <select name="bhk" class="form-control form-control-custom" <?= (strpos(strtolower($p['type']), 'commercial') === false) ? 'required' : '' ?>>
                                        <option value="">Select BHK</option>
                                        <?php $bhks=["1 Room","2 Room","1 RK","2 RK","1 BHK","1.5 BHK","2 BHK","2.5 BHK","3 BHK","3.5 BHK","4 BHK & More"];
                                        foreach($bhks as $b){ $sel=($p['bhk']==$b)?'selected':''; echo "<option $sel>$b</option>"; } ?>
                                    </select>
                                    <i class="fas fa-home input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Property Condition</label>
                                <div class="form-group-custom">
                                    <select name="property_condition" class="form-control form-control-custom" required>
                                        <?php $conds=["Furnished","Fix Furnished","Fully Furnished","Kitchen Fix","Semi Furnished","Unfurnished"];
                                        foreach($conds as $c){ $sel=($p['property_condition']==$c)?'selected':''; echo "<option $sel>$c</option>"; } ?>
                                    </select>
                                    <i class="fas fa-couch input-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="label-custom">Tenant Preference / Type</label>
                                <div class="form-group-custom">
                                    <select name="availability" id="availabilityField" class="form-control form-control-custom" required>
                                        <!-- Will be populated by JS -->
                                    </select>
                                    <i class="fas fa-users input-icon"></i>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="label-custom">Google Maps Link</label>
                                <div class="form-group-custom">
                                    <input type="url" name="address_link" class="form-control form-control-custom" value="<?= htmlspecialchars($p['address_link']) ?>" placeholder="Paste Google Maps URL here">
                                    <i class="fas fa-link input-icon"></i>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="label-custom">Full Address</label>
                                <div class="form-group-custom">
                                    <textarea name="address" class="form-control form-control-custom" rows="3" required><?= htmlspecialchars($p['address']) ?></textarea>
                                    <i class="fas fa-map input-icon textarea-icon"></i>
                                </div>
                            </div>

                            <!-- Existing Media Gallery -->
                            <div class="col-12 mb-4">
                                <label class="label-custom">Current Property Media (Hover to Delete)</label>
                                <div class="media-gallery-container shadow-sm p-3 rounded-4 bg-white border border-light-subtle">
                                    <?php
                                    // Images
                                    $imgs = mysqli_query($con, "SELECT id, image FROM tbl_property_images WHERE property_id=$id");
                                    if (mysqli_num_rows($imgs) > 0) {
                                        while ($img = mysqli_fetch_assoc($imgs)) {
                                            ?>
                                            <div class="media-tile shadow-sm rounded-3 overflow-hidden position-relative" id="media_img_<?= $img['id'] ?>">
                                                <img src="../uploads/<?= htmlspecialchars($img['image']) ?>" alt="Property Image">
                                                <div class="delete-media-overlay deleteMediaBtn" data-id="<?= $img['id'] ?>" data-type="image">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                                <div class="media-tile-badge">IMG</div>
                                            </div>
                                            <?php
                                        }
                                    }

                                    // Videos
                                    $vids = mysqli_query($con, "SELECT id, video FROM tbl_property_videos WHERE property_id=$id");
                                    if (mysqli_num_rows($vids) > 0) {
                                        while ($vid = mysqli_fetch_assoc($vids)) {
                                            ?>
                                            <div class="media-tile media-tile-video shadow-sm rounded-3 overflow-hidden position-relative" id="media_vid_<?= $vid['id'] ?>">
                                                <i class="fas fa-play-circle"></i>
                                                <div class="delete-media-overlay deleteMediaBtn" data-id="<?= $vid['id'] ?>" data-type="video">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                                <div class="media-tile-badge">VID</div>
                                            </div>
                                            <?php
                                        }
                                    }

                                    if (mysqli_num_rows($imgs) == 0 && mysqli_num_rows($vids) == 0) {
                                        echo '<p class="text-muted small mb-0 w-100 text-center py-4"><i class="fas fa-cloud-upload-alt me-2"></i> No media uploaded yet.</p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label class="label-custom">Add More Images</label>
                                <div class="file-group-custom">
                                    <label class="file-input-label" for="property_images">
                                        <i class="fas fa-images"></i> <span>Click to upload more images</span>
                                        <input type="file" name="images[]" id="property_images" class="visually-hidden" accept="image/*" multiple>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label class="label-custom">Add More Videos</label>
                                <div class="file-group-custom">
                                    <label class="file-input-label" for="property_videos">
                                        <i class="fas fa-video"></i> <span>Click to upload more videos</span>
                                        <input type="file" name="videos[]" id="property_videos" class="visually-hidden" accept="video/*" multiple>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <label class="label-custom">Additional Description</label>
                                <div class="form-group-custom">
                                    <textarea name="description" class="form-control form-control-custom" rows="4"><?= htmlspecialchars($p['description']) ?></textarea>
                                    <i class="fas fa-info-circle input-icon textarea-icon"></i>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-premium w-100">
                                    <i class="fas fa-save me-2"></i> Update Property Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const propertyType = document.getElementById("propertyType");
    const bhkWrapper = document.getElementById("bhkWrapper");
    const availabilityField = document.getElementById("availabilityField");
    const form = document.querySelector("form");

    const currentAvailability = "<?= $p['availability'] ?>";

    function updateFields() {
        let value = propertyType.value.toLowerCase();
        const bhkSelect = bhkWrapper.querySelector("select");

        if (value.includes("commercial")) {
            bhkWrapper.style.display = "none";
            bhkSelect.removeAttribute("required");
            
            const options = ["Basement","Commercial Space","Pre-Lease Property","Ware House","Co Working Space","Factory","Restaurant","Commercial Building","Godown","Shed","Commercial Bungalow","Industrial Land","Shop","Commercial Flat","Industrial Shed","Showroom","Commercial Plot","Office","Space"];
            let html = '<option value="">Select Type</option>';
            options.forEach(o => {
                let sel = (currentAvailability.trim() === o.trim()) ? 'selected' : '';
                html += `<option ${sel}>${o}</option>`;
            });
            availabilityField.innerHTML = html;
        } else {
            bhkWrapper.style.display = "block";
            bhkSelect.setAttribute("required", true);
            
            const options = ["Family","Bachelor","Family & Bachelor"];
            let html = '<option value="">Select Preference</option>';
            options.forEach(o => {
                let sel = (currentAvailability.trim() === o.trim()) ? 'selected' : '';
                html += `<option ${sel}>${o}</option>`;
            });
            availabilityField.innerHTML = html;
        }
    }

    propertyType.addEventListener("change", updateFields);
    updateFields(); // Initial call

    form.addEventListener("submit", function(e) {
        if (!form.checkValidity()) return;
        const submitBtn = form.querySelector('button[type="submit"]');
        setTimeout(() => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Updating...';
        }, 50);
    });

    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files.length > 1 ? `${e.target.files.length} files selected` : (e.target.files[0] ? e.target.files[0].name : "Click to upload");
            const labelSpan = this.parentElement.querySelector('span');
            labelSpan.innerText = fileName; labelSpan.classList.add('text-primary', 'fw-bold');
        });
    });
});
</script>
<?php include "layout-footer.php"; ?>
