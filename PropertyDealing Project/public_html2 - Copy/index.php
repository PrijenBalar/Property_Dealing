<?php
include "includes/config.php";
include "includes/header.php";

/* =========================
   SHOW FILTER TOGGLE
========================= */
$showFilter = (isset($_GET['showFilter']) && $_GET['showFilter'] == '1');

/* =========================
   RESET FILTER
========================= */
if(isset($_GET['reset'])){
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

/* =========================
   NORMALIZE GET VALUES
========================= */
function getArray($key){
  if(!isset($_GET[$key])) return [];
  if(is_array($_GET[$key])) return array_filter($_GET[$key]);
  return [$_GET[$key]];
}

$areas  = getArray('area');
$propertyTypes  = getArray('type');
$bhks   = getArray('bhk');
$avail  = getArray('availability');
$cond   = getArray('property_condition');

// Predefined area list used for Area filter/search (NOT from DB)
$AREA_OPTIONS = [
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

$minP = ($_GET['min_price'] ?? '') !== '' ? (int)$_GET['min_price'] : null;
$maxP = ($_GET['max_price'] ?? '') !== '' ? (int)$_GET['max_price'] : null;

/* =========================
   BUILD WHERE CONDITION
========================= */
$whereParts = ["p.status = ?"];
$paramTypes = 's';
$values = ['approved'];

if (!empty($areas)) {
    $placeholders = implode(',', array_fill(0, count($areas), '?'));
    $whereParts[] = "p.area IN ($placeholders)";
    $paramTypes .= str_repeat('s', count($areas));
    array_push($values, ...$areas);
}

if (!empty($propertyTypes)) {
    $placeholders = implode(',', array_fill(0, count($propertyTypes), '?'));
    $whereParts[] = "p.type IN ($placeholders)";
    $paramTypes .= str_repeat('s', count($propertyTypes));
    array_push($values, ...$propertyTypes);
}

if (!empty($bhks)) {
    $placeholders = implode(',', array_fill(0, count($bhks), '?'));
    $whereParts[] = "p.bhk IN ($placeholders)";
    $paramTypes .= str_repeat('s', count($bhks));
    array_push($values, ...$bhks);
}
/* =========================
   AVAILABILITY FILTER (FINAL & CORRECT)
========================= */
if (!empty($avail)) {

    $availabilityConditions = [];

    if (in_array('Bachelor', $avail)) {
        $availabilityConditions[] = "p.availability LIKE '%Bachelor%'";
    }

    if (in_array('Family', $avail)) {
        $availabilityConditions[] = "p.availability LIKE '%Family%'";
    }

    if (!empty($availabilityConditions)) {
        $whereParts[] = "(" . implode(" OR ", $availabilityConditions) . ")";
    }
}


$where = " WHERE " . implode(" AND ", $whereParts);

/* =========================
   FETCH PROPERTIES
========================= */
$query = "
    SELECT
        p.*,
        GROUP_CONCAT(DISTINCT pi.image ORDER BY pi.id) as all_images_str,
        GROUP_CONCAT(DISTINCT pv.video ORDER BY pv.id) as all_videos_str,
        COUNT(DISTINCT pi.id) as image_count,
        COUNT(DISTINCT pv.id) as video_count
    FROM
        tbl_property p
    LEFT JOIN
        tbl_property_images pi ON p.id = pi.property_id
    LEFT JOIN
        tbl_property_videos pv ON p.id = pv.property_id
    $where
    GROUP BY
        p.id
    ORDER BY p.area, p.id DESC";

$q = false; // Default to false in case of query failure
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    if (!empty($values)) {
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$values);
    }
    mysqli_stmt_execute($stmt);
    $q = mysqli_stmt_get_result($stmt);
}
?>

<!-- ================= FILTER BAR ================= -->

<div class="collapse" id="mainFilterBar">
  <div class="container-fluid filter-bar-wrapper py-3">

  <form method="GET" id="filterForm">

<input type="hidden" name="showFilter" value="1">
<div class="filter-bar-card shadow-sm">
<div class="row g-1 align-items-stretch">

<!-- AREA -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('areaBox')">
  <span class="filter-label">Area</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="areaBox">
  <div class="border rounded bg-white p-2 filter-dropdown-inner">
    <input type="text"
           id="areaSearch"
           class="form-control form-control-sm mb-2"
           placeholder="Search area">

    <!-- Checkbox list of predefined areas -->
    <div id="areaList" style="max-height:155px;overflow:auto;">
      <?php foreach($AREA_OPTIONS as $a){ ?>
        <div class="form-check area-item" data-label="<?= strtolower($a) ?>">
          <input class="form-check-input"
                 type="checkbox"
                 name="area[]"
                 value="<?= $a ?>"
                 id="area_<?= md5($a) ?>"
                 <?= in_array($a,$areas)?'checked':'' ?>>
          <label class="form-check-label small" for="area_<?= md5($a) ?>"><?= $a ?></label>
        </div>
<?php } ?>
    </div>
  </div>
</div>
</div>

<!-- PROPERTY TYPE -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('typeBox')">
  <span class="filter-label">Property Type</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="typeBox">
  <div class="border rounded bg-white p-2 filter-dropdown-inner">
<?php foreach(["Residential Rent","Residential Sell","Commercial Rent","Commercial Sell"] as $t){ 
    $isComm = strpos($t, 'Commercial') !== false;
?>
    <div class="form-check">
      <input class="form-check-input type-checkbox <?= $isComm ? 'commercial-type' : 'residential-type' ?>"
             type="checkbox"
             name="type[]"
             value="<?= $t ?>"
             id="type_<?= md5($t) ?>"
             <?= in_array($t,$propertyTypes)?'checked':'' ?>>
      <label class="form-check-label small" for="type_<?= md5($t) ?>"><?= $t ?></label>
    </div>
<?php } ?>
  </div>
</div>
</div>

<!-- BHK -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col" id="bhkFilterCol">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('bhkBox')">
  <span class="filter-label">BHK</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="bhkBox">
  <div class="border rounded bg-white p-2 filter-dropdown-inner">
<?php foreach(["1 Room","2 Room","1 RK","2 RK","1 BHK","1.5 BHK","2 BHK","2.5 BHK","3 BHK","3.5 BHK","4 BHK & More"] as $b){ ?>
    <div class="form-check">
      <input class="form-check-input"
             type="checkbox"
             name="bhk[]"
             value="<?= $b ?>"
             id="bhk_<?= md5($b) ?>"
             <?= in_array($b,$bhks)?'checked':'' ?>>
      <label class="form-check-label small" for="bhk_<?= md5($b) ?>"><?= $b ?></label>
    </div>
<?php } ?>
  </div>
</div>
</div>

<!-- AVAILABILITY / COMMERCIAL SUBTYPES -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('availBox')">
  <span class="filter-label" id="availFilterLabel">Availability</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="availBox">
  <div class="border rounded bg-white p-2 filter-dropdown-inner">
  
    <!-- Residential Availability -->
    <div id="resAvailItems">
      <?php foreach(["Family","Bachelor"] as $v){ ?>
        <div class="form-check">
          <input class="form-check-input"
                 type="checkbox"
                 name="availability[]"
                 value="<?= $v ?>"
                 id="avail_<?= md5($v) ?>"
                 <?= in_array($v,$avail)?'checked':'' ?>>
          <label class="form-check-label small" for="avail_<?= md5($v) ?>"><?= $v ?></label>
        </div>
      <?php } ?>
    </div>

    <!-- Commercial Subtypes -->
    <div id="commAvailItems" style="display:none; max-height: 250px; overflow-y: auto;">
      <?php
      $commOptions = [
        "Basement", "Commercial Space", "Pre-Lease Property", "Ware House", "Co Working Space",
        "Factory", "Restaurant", "Commercial Building", "Godown", "Shed",
        "Commercial Bungalow", "Industrial Land", "Shop", "Commercial Flat", "Industrial Shed",
        "Showroom", "Commercial Plot", "Office", "Space"
      ];
      foreach($commOptions as $v){ ?>
        <div class="form-check">
          <input class="form-check-input"
                 type="checkbox"
                 name="availability[]"
                 value="<?= $v ?>"
                 id="avail_<?= md5($v) ?>"
                 <?= in_array($v,$avail)?'checked':'' ?>>
          <label class="form-check-label small" for="avail_<?= md5($v) ?>"><?= $v ?></label>
        </div>
      <?php } ?>
    </div>

  </div>
</div>
</div>

<!-- CONDITION -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('condBox')">
  <span class="filter-label">Condition</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="condBox">
  <div class="border rounded bg-white p-2 filter-dropdown-inner">
<?php foreach([
  "Furnished",
  "Fix Furnished",
  "Fully Furnished",
  "Kitchen fix",
  "Semi furnished",
  "unfurnished"
] as $c){ ?>
    <div class="form-check">
      <input class="form-check-input"
             type="checkbox"
             name="property_condition[]"
             value="<?= $c ?>"
             id="cond_<?= md5($c) ?>"
             <?= in_array($c,$cond)?'checked':'' ?>>
      <label class="form-check-label small" for="cond_<?= md5($c) ?>"><?= $c ?></label>
    </div>
<?php } ?>
  </div>
</div>
</div>

<!-- BUDGET -->
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 filter-col">
<button type="button" class="btn filter-pill w-100"
onclick="toggleFilter('priceBox')">
  <span class="filter-label">Budget</span>
  <span class="filter-arrow"></span>
</button>
<div class="collapse mt-2 filter-dropdown" id="priceBox">
<div class="border rounded bg-white p-2 filter-dropdown-inner">
<input type="number" name="min_price" value="<?= $minP ?>" class="form-control mb-2" placeholder="Min Price">
<input type="number" name="max_price" value="<?= $maxP ?>" class="form-control" placeholder="Max Price">
</div>
</div>
</div>

<!-- APPLY -->
<div class="col-xl-auto col-lg-3 col-md-4 col-sm-6 filter-action ms-xl-auto">
<button type="submit" class="btn filter-apply-btn w-100">Apply</button>
</div>

</div><!-- /.row -->
</div><!-- /.filter-bar-card -->
</form>
</div>
</div>


<!-- ALWAYS-VISIBLE BANNER IMAGE WITH OVERLAY TEXT -->
<div class="container-fluid p-0 hero-section">
  <div class="position-relative hero-wrapper">
    <img src="assets/images/propertyimg.jpg"
         alt="Property banner"
         class="w-100 h-100 hero-image">

    <!-- Dark gradient overlay -->
    <div class="hero-gradient"></div>

    <!-- Overlay text -->
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white hero-text">
      <h1 class="fw-bold mb-2 hero-title">Find Your Dream Property in Ahmedabad</h1>
      <p class="mb-0 hero-subtitle">Premium residential and commercial spaces curated just for you.</p>
    </div>
  </div>
</div>

<!-- ================= PROPERTY LIST (AREA WISE SCROLL) ================= -->
<div class="container mt-4">

<?php
// GROUP PROPERTIES BY AREA
$propertiesByArea = [];
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $propertiesByArea[$row['area']][] = $row;
    }
}

if (empty($propertiesByArea)) {
  echo "<p class='text-muted'>No properties found</p>";
}

// LOOP ONLY AREAS WHICH EXIST IN DB
foreach($propertiesByArea as $areaName => $rows):
?>

  <!-- AREA TITLE -->
  <h4 class="fw-bold mt-5 mb-3">
    <?= htmlspecialchars($areaName) ?>
  </h4>

  <!-- HORIZONTAL SCROLL -->
  <div class="area-scroll-row mb-4">

  <?php foreach($rows as $row):

    $all_images = [];
    if (!empty($row['all_images_str'])) {
        $all_images = explode(',', $row['all_images_str']);
    }
    $main_image = $all_images[0] ?? null;

    $imagePath = "assets/images/no_image2.png";
    if ($main_image) {
        $imagePath = "uploads/" . $main_image;
    }

    $returnUrl = urlencode($_SERVER['REQUEST_URI']);

    // --- WhatsApp Share Logic ---
    // --- WhatsApp Share Logic ---
$relativePropertyUrl = "property-details.php?id=" . $row['id'];

$fullPropertyUrl = "https://kargilproperty.com/" . $relativePropertyUrl;

$shareDetailsText =
  "Check out this property!\n\n" .
  "🆔 Property ID: " . htmlspecialchars($row['id']) . "\n" .
  "🏠 Property: " . htmlspecialchars($row['type']) . "\n" .
  "🛏 BHK: " . htmlspecialchars($row['bhk']) . "\n" .
  "📍 Area: " . htmlspecialchars($row['area']) . "\n" .
  "🧾 Condition: " . htmlspecialchars($row['property_condition']) . "\n" .
  "👨‍👩‍👦 Availability: " . html_entity_decode($row['availability']) . "\n".
  "💰 Price: ₹ " . htmlspecialchars($row['price']);


$whatsappShareText =
  $shareDetailsText . "\n\nSee more details here:\n" . $fullPropertyUrl;

$whatsappShareLink =
  "https://api.whatsapp.com/send?text=" . rawurlencode($whatsappShareText);
// --- End WhatsApp Share Logic ---

  ?>

    <div class="area-scroll-item">
      <div class="modern-property-card h-100">

        <a href="property-details.php?id=<?= $row['id'] ?>&return=<?= $returnUrl ?>"
           class="property-image-wrapper">
          <img src="<?= htmlspecialchars($imagePath) ?>"
     class="property-image"
     alt="Image not available"
     onerror="this.onerror=null;this.src='assets/images/no_image2.png';">

          <div class="property-image-overlay"></div>

          <div class="property-badge-price">
            ₹ <?= htmlspecialchars($row['price']) ?>

          </div>

          <div class="property-overlay-content">
            <div class="property-overlay-title">
              <?= $row['type'] ?> • <?= $row['bhk'] ?>
            </div>
            <div class="property-overlay-location">
              <i class="bi bi-geo-alt me-1"></i><?= $row['area'] ?>
            </div>
          </div>
        </a>

        <div class="property-card-body d-flex flex-column">
          <div class="property-meta mb-2">
            <span class="availability"><?= $row['availability'] ?></span>
            <span class="dot-separator">•</span>
            <span class="condition"><?= $row['property_condition'] ?></span>
          </div>

          <div class="property-icons-row mb-3">
            <div class="icon-item"><i class="bi bi-bed"></i><?= $row['bhk'] ?></div>
            <!-- <div class="icon-item"><i class="bi bi-droplet"></i>Bath</div>
            <div class="icon-item"><i class="bi bi-aspect-ratio"></i>Area</div> -->
          </div>

          <div class="mt-auto d-flex justify-content-between align-items-center">
  <div class="d-flex gap-2">
    <a href="property-details.php?id=<?= $row['id'] ?>&return=<?= $returnUrl ?>"
       class="btn view-details-btn">
      View Details
    </a>

    <!-- WhatsApp Share Button (Works on Desktop & Mobile) -->
    <a href="<?= $whatsappShareLink ?>"
       class="btn btn-outline-success"
       target="_blank"
       rel="noopener"
       aria-label="Share on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
  </div>

  <small class="text-muted">#<?= $row['id'] ?></small>
</div>

        </div>

      </div>
    </div>

  <?php endforeach; ?>

  </div>

<?php endforeach; ?>

</div>


<?php include "includes/footer.php"; ?>

<link rel="stylesheet" href="assets/css/index.css">

<div class="social-float">
  <a href="https://www.instagram.com/kargil_property?igsh=MTM1YXZ3dzhleXdlZA==" 
     class="instagram"
     target="_blank" 
     rel="noopener">
    <i class="bi bi-instagram"></i>
  </a>
  <a href="https://www.facebook.com/share/1AbRywaDLo/" 
     class="facebook"
     target="_blank" 
     rel="noopener">
    <i class="bi bi-facebook"></i>
  </a>

  <!-- Call button -->
  <a href="tel:+918758257487"
     class="call"
     aria-label="Call Kargil Property">
    <i class="bi bi-telephone-fill"></i>
  </a>
</div>











