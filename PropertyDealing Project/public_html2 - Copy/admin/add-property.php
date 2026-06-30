<?php include "layout-header.php"; ?>
<?php
include "../includes/config.php";
include "../includes/auth.php";


/* =====================
   HELPER: CONVERT ANY IMAGE TO WEBP
===================== */
function convertToWebp($sourcePath, $destinationPath, $quality = 80) {
    $info = getimagesize($sourcePath);
    if (!$info) return false;

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;

        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;

        case 'image/webp':
            return move_uploaded_file($sourcePath, $destinationPath);

        default:
            return false;
    }

    $result = imagewebp($image, $destinationPath, $quality);
    imagedestroy($image);
    return $result;
}

$message = "";
$areas = [
  "SG Road",
  "Akhbarnagar",
  "Ambawadi",
  "Ambli",
  "Ashram Road",
  "Bapunagar",
  "Bhadaj",
  "Bhuyangdev",
  "Bodakdev",
  "Bopal",
  "CG Road",
  "Chanakyapuri",
  "Chandkheda",
  "Chandlodia",
  "Drive In Road",
  "Ellisbridge",
  "Gandhinagar",
  "Ghatlodia",
  "Ghuma",
  "Gift City",
  "Gota",
  "Gulbai Tekra",
  "Gurukul",
  "Hebatpur",
  "Income Tax",
  "Iscon Ambli Road",
  "Jagatpur",
  "Jivraj Park",
  "Jodhpur",
  "Juhapura",
  "Kudasan",
  "Law Garden",
  "Makarba",
  "Manekbaug",
  "Manipur",
  "Memnagar",
  "Mithakhali",
  "Motera",
  "Naroda",
  "Naranpura",
  "Navrangpura",
  "Nehru Nagar",
  "New CG Road",
  "New Ranip",
  "New Wadaj",
  "Nikol",
  "Nirnay Nagar",
  "Ognaj",
  "Paldi",
  "Prahlad Nagar",
  "Ramdevnagar",
  "Ranip",
  "Sabarmati",
  "Sanathal",
  "Santej",
  "Sargasan",
  "Sarkhej",
  "Satadhar",
  "Satellite",
  "Science City",
  "Shahibaug",
  "Shastrinagar",
  "Shela",
  "Shilaj",
  "Shivranjani",
  "Shyamal",
  "Sindhubhavan Road",
  "Sola",
  "South Bopal",
  "SP Ring Road",
  "Subhash Bridge",
  "Surdhara Circle",
  "Thaltej",
  "Tragad",
  "Usmanpura",
  "Vahelal",
  "Vaishno Devi",
  "Vasna",
  "Vastral",
  "Vastrapur",
  "Vejalpur",
  "Vijay Cross Road",
  "Wadaj",
  "Zundal"
];





if (isset($_POST['add_property'])) {

    $owner_name   = mysqli_real_escape_string($con, trim($_POST['owner_name']));
    $owner_mobile = mysqli_real_escape_string($con, trim($_POST['owner_mobile']));
    $price        = mysqli_real_escape_string($con, trim($_POST['price']));
    $type         = mysqli_real_escape_string($con, $_POST['property_type']);
    $area         = mysqli_real_escape_string($con, $_POST['area']);
    $bhk          = mysqli_real_escape_string($con, $_POST['bhk']);
    $condition    = mysqli_real_escape_string($con, $_POST['property_condition']);
    $availability = mysqli_real_escape_string($con, $_POST['availability']);
    $address      = mysqli_real_escape_string($con, trim($_POST['address']));
    $address_link = mysqli_real_escape_string($con, trim($_POST['address_link']));
    $description  = mysqli_real_escape_string($con, trim($_POST['description']));

    /* =====================
       VALIDATION
    ===================== */
    if (!preg_match("/^[a-zA-Z0-9 ]+$/", $owner_name)) {
    $message = "Owner name can contain letters, numbers and spaces only";
}

    elseif (!preg_match("/^[0-9]{10}$/", $owner_mobile)) {
        $message = "Owner mobile must be 10 digits";
    }
    else {

        /* =====================
           DUPLICATE MOBILE CHECK
        ===================== */
        $check = mysqli_query(
            $con,
            "SELECT id FROM tbl_property WHERE owner_mobile='$owner_mobile' LIMIT 1"
        );

        if (mysqli_num_rows($check) > 0) {
            $_SESSION['error'] = "This mobile number already exists. Please use another number.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        else {

            /* =====================
               INSERT PROPERTY
            ===================== */
            mysqli_query($con, "
                INSERT INTO tbl_property
                (
                    owner_name, owner_mobile, price, type, area, bhk,
                    property_condition, availability, address, address_link,
                    description, status
                )
                VALUES
                (
                    '$owner_name','$owner_mobile','$price','$type','$area','$bhk',
                    '$condition','$availability','$address','$address_link',
                    '$description','pending'
                )
            ");

            $property_id = mysqli_insert_id($con);

            /* =====================
               IMAGE UPLOAD (FORCE WEBP)
            ===================== */
            if (!empty($_FILES['images']['name'][0])) {

                $allowed_img = ['jpg','jpeg','png','webp'];

                foreach ($_FILES['images']['name'] as $key => $name) {

                    if (!$name) continue;

                    $tmp = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed_img)) continue;

                    $img  = uniqid('img_') . '.webp';
                    $dest = "../uploads/" . $img;

                    if (convertToWebp($tmp, $dest, 80)) {
                        mysqli_query(
                            $con,
                            "INSERT INTO tbl_property_images(property_id,image)
                             VALUES('$property_id','$img')"
                        );
                    }
                }
            }

            /* =====================
               VIDEO UPLOAD (AS-IS)
            ===================== */
            if (!empty($_FILES['videos']['name'][0])) {

                foreach ($_FILES['videos']['name'] as $key => $name) {

                    if (!$name) continue;

                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $vid = uniqid('vid_') . '.' . $ext;

                    move_uploaded_file(
                        $_FILES['videos']['tmp_name'][$key],
                        "../uploads/" . $vid
                    );

                    mysqli_query(
                        $con,
                        "INSERT INTO tbl_property_videos(property_id,video)
                         VALUES('$property_id','$vid')"
                    );
                }
            }

            $_SESSION['success'] = "Property added successfully (Pending Approval)";
            header("Location: manage-property.php");
            exit;
        }
    }
}
?>



<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="premium-card animate-fade-in shadow-lg">
                <div class="premium-card-header text-center">
                    <h4><i class="fas fa-plus-circle"></i> Add New Property</h4>
                    <p class="mb-0 text-white-50 small">Fill in the details to list a new property</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <?php if ($message): ?>
                        <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" autocomplete="off" class="needs-validation" novalidate>
                        <input type="hidden" name="add_property" value="1">
                        <div class="row">
                            <!-- Owner Name -->
                            <div class="col-md-6">
                                <label class="label-custom">Owner Name</label>
                                <div class="form-group-custom">
                                    <input type="text" name="owner_name" id="owner_name" class="form-control form-control-custom" placeholder="e.g. John Doe" required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Owner name is required (Letters & spaces only).</div>
                            </div>

                            <!-- Owner Mobile -->
                            <div class="col-md-6">
                                <label class="label-custom">Mobile Number</label>
                                <div class="form-group-custom">
                                    <input type="text" name="owner_mobile" id="owner_mobile" class="form-control form-control-custom" placeholder="10-digit number" maxlength="10" required>
                                    <i class="fas fa-phone-alt input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                <div id="mobileWarning" class="text-danger fw-bold mb-3 small" style="display:none; margin-top: -10px; margin-left: 5px;">
                                    <i class="fas fa-exclamation-triangle"></i> This mobile number already exists.
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label class="label-custom">Price</label>
                                <div class="form-group-custom">
                                    <input type="text" name="price" id="price" class="form-control form-control-custom" placeholder="e.g. 65 LK or 25,000" required>
                                    <i class="fas fa-tag input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Price field cannot be empty.</div>
                            </div>

                            <!-- Property Type -->
                            <div class="col-md-6">
                                <label class="label-custom">Property Type</label>
                                <div class="form-group-custom">
                                    <select name="property_type" id="propertyType" class="form-control form-control-custom" required>
                                        <option value="">Select Type</option>
                                        <option>Residential Rent</option>
                                        <option>Residential Sell</option>
                                        <option>Commercial Rent</option>
                                        <option>Commercial Sell</option>
                                    </select>
                                    <i class="fas fa-building input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Please select a property type.</div>
                            </div>

                            <!-- Area -->
                            <div class="col-md-6">
                                <label class="label-custom">Area / Location</label>
                                <div class="form-group-custom">
                                    <select name="area" id="area" class="form-control form-control-custom" required>
                                        <option value="">Select Area</option>
                                        <?php foreach ($areas as $area) { ?>
                                            <option value="<?= htmlspecialchars($area) ?>">
                                                <?= htmlspecialchars($area) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Please select an area.</div>
                            </div>

                            <!-- BHK (Dynamic) -->
                            <div class="col-md-6" id="bhkWrapper">
                                <label class="label-custom">BHK / Configuration</label>
                                <div class="form-group-custom">
                                    <select name="bhk" id="bhk" class="form-control form-control-custom" required>
                                        <option value="">Select BHK</option>
                                        <option>1 Room</option>
                                        <option>2 Room</option>
                                        <option>1 RK</option>
                                        <option>2 RK</option>
                                        <option>1 BHK</option>
                                        <option>1.5 BHK</option>
                                        <option>2 BHK</option>
                                        <option>2.5 BHK</option>
                                        <option>3 BHK</option>
                                        <option>3.5 BHK</option>
                                        <option>4 BHK & More</option>
                                    </select>
                                    <i class="fas fa-home input-icon"></i>
                                </div>
                                <div class="invalid-feedback">BHK configuration is required.</div>
                            </div>

                            <!-- Condition -->
                            <div class="col-md-6">
                                <label class="label-custom">Property Condition</label>
                                <div class="form-group-custom">
                                    <select name="property_condition" id="property_condition" class="form-control form-control-custom" required>
                                        <option value="">Select Condition</option>
                                        <option>Furnished</option>
                                        <option>Fix Furnished</option>
                                        <option>Fully Furnished</option>
                                        <option>Kitchen Fix</option>
                                        <option>Semi Furnished</option>
                                        <option>Unfurnished</option>
                                    </select>
                                    <i class="fas fa-couch input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Condition is required.</div>
                            </div>

                            <!-- Availability (Dynamic) -->
                            <div class="col-md-6">
                                <label class="label-custom">Tenant Preference / Type</label>
                                <div class="form-group-custom">
                                    <select name="availability" id="availabilityField" class="form-control form-control-custom" required>
                                        <option value="">Select Preference</option>
                                        <option>Family</option>
                                        <option>Bachelor</option>
                                        <option>Family & Bachelor</option>
                                    </select>
                                    <i class="fas fa-users input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Preference is required.</div>
                            </div>

                            <!-- Map Link -->
                            <div class="col-12">
                                <label class="label-custom">Google Maps Link</label>
                                <div class="form-group-custom">
                                    <input type="url" name="address_link" class="form-control form-control-custom" placeholder="Paste Google Maps URL here">
                                    <i class="fas fa-link input-icon"></i>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label class="label-custom">Full Address</label>
                                <div class="form-group-custom">
                                    <textarea name="address" id="address" class="form-control form-control-custom" rows="3" placeholder="Enter detailed property address" required></textarea>
                                    <i class="fas fa-map input-icon textarea-icon"></i>
                                </div>
                                <div class="invalid-feedback">Physical address is mandatory.</div>
                            </div>

                            <!-- Media Uploads -->
                            <div class="col-md-6 mt-2">
                                <label class="label-custom">Property Images</label>
                                <div class="file-group-custom">
                                        <label class="file-input-label" for="property_images">
                                            <i class="fas fa-images"></i>
                                            <span>Click to upload images</span>
                                            <input type="file" name="images[]" id="property_images" class="visually-hidden" accept="image/*" multiple>
                                        </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label class="label-custom">Property Videos</label>
                                <div class="file-group-custom">
                                        <label class="file-input-label" for="property_videos">
                                            <i class="fas fa-video"></i>
                                            <span>Click to upload videos</span>
                                            <input type="file" name="videos[]" id="property_videos" class="visually-hidden" accept="video/*" multiple>
                                        </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mt-4">
                                <label class="label-custom">Additional Description</label>
                                <div class="form-group-custom">
                                    <textarea name="description" class="form-control form-control-custom" rows="4" placeholder="Features, landmarks, or special notes..."></textarea>
                                    <i class="fas fa-info-circle input-icon textarea-icon"></i>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-premium w-100">
                                    <i class="fas fa-plus-circle me-2"></i> Add Property Listings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include "layout-footer.php"; ?>
