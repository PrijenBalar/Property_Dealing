<?php
include "includes/config.php";

/* ======================
   FETCH PROPERTY
====================== */
$id = (int)($_GET['id'] ?? 0);
$p = null;

if ($id > 0) {
    $stmt = mysqli_prepare(
        $con,
        "SELECT * FROM tbl_property WHERE id = ? AND status='approved' LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $p = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt); // ✅ performance improvement
}

/* ======================
   HEADER
====================== */
$hideFilter = true;
include "includes/header.php";

/* ======================
   VALIDATION
====================== */
if (!$p) {
    echo "<div class='container mt-5'><h4>Property not found</h4></div>";
    include "includes/footer.php";
    exit;
}

/* ======================
   IMAGES
====================== */
$images = [];
$q = mysqli_prepare(
    $con,
    "SELECT image FROM tbl_property_images WHERE property_id=?"
);
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);
$r = mysqli_stmt_get_result($q);

while ($img = mysqli_fetch_assoc($r)) {
    $images[] = $img['image'];
}
mysqli_stmt_close($q); // ✅ performance

/* ======================
   VIDEOS
====================== */
$videos = [];
$qv = mysqli_prepare(
    $con,
    "SELECT video FROM tbl_property_videos WHERE property_id=?"
);
mysqli_stmt_bind_param($qv, "i", $id);
mysqli_stmt_execute($qv);
$rv = mysqli_stmt_get_result($qv);

while ($vid = mysqli_fetch_assoc($rv)) {
    $videos[] = $vid['video'];
}
mysqli_stmt_close($qv); // ✅ performance

/* ======================
   WHATSAPP MESSAGE
====================== */
$whatsapp_number = "918758257487"; // ✅ FIXED NUMBER

$message = rawurlencode(
    "Hi, I am interested in Property ID #".$p['id'].
    "\nType: ".$p['type'].
    "\nBHK: ".$p['bhk'].
    "\nArea: ".$p['area'].
    "\nPrice: ₹ ".$p['price']
);
?>

<div class="container container-fluid py-5">
  <!-- BACK BUTTON -->
  <div class="mb-4">
    <button id="backBtn" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-medium">
      <i class="bi bi-arrow-left me-2"></i>Back to Properties
    </button>
  </div>

  <div class="row g-4">
    <!-- LEFT COLUMN: Images & Details -->
    <div class="col-lg-8">
      
      <!-- IMAGE SLIDER -->
      <div id="carousel" class="carousel slide mb-5 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php if ($images) {
            foreach ($images as $k => $img) { ?>
              <div class="carousel-item <?= $k === 0 ? 'active' : '' ?>">
                <img src="uploads/<?= htmlspecialchars($img) ?>"
                     class="d-block w-100"
                     style="height:500px;object-fit:cover"
                     loading="lazy">
              </div>
          <?php }} else { ?>
            <div class="carousel-item active">
              <img src="assets/images/no_image2.png"
                   class="d-block w-100"
                   style="height:500px;object-fit:cover"
                   loading="lazy">
            </div>
          <?php } ?>
        </div>

        <?php if (count($images) > 1) { ?>
          <button class="carousel-control-prev" type="button"
                  data-bs-target="#carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button"
                  data-bs-target="#carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        <?php } ?>
      </div>

      <!-- DESCRIPTION -->
      <?php if (!empty($p['description'])) { ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">Property Description</h5>
            <p class="text-secondary lh-lg mb-0" style="font-size:0.95rem;">
              <?= nl2br(htmlspecialchars($p['description'])) ?>
            </p>
          </div>
        </div>
      <?php } ?>

      <!-- VIDEOS -->
      <?php if (!empty($videos)) { ?>
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4 text-dark">Property Videos</h5>
          <div class="row g-4">
            <?php foreach ($videos as $video) { ?>
              <div class="col-md-6">
                <?php if (str_contains($video, 'youtube') || str_contains($video, 'youtu.be')) { ?>
                  <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                    <iframe src="<?= htmlspecialchars($video) ?>" allowfullscreen></iframe>
                  </div>
                <?php } else { ?>
                  <video controls class="w-100 rounded-3 shadow-sm"
                         style="max-height:250px; object-fit:cover">
                    <source src="uploads/<?= htmlspecialchars($video) ?>" type="video/mp4">
                  </video>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>

    <!-- RIGHT COLUMN: Sticky Info & Contact -->
    <div class="col-lg-4">
      <div class="sticky-top" style="top: 100px; z-index: 1000;">
        <div class="card shadow-lg border-0 rounded-4 mb-4">
          <div class="card-body p-4">
            
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-primary text-uppercase px-3 py-2 rounded-pill fs-7">
                <?= htmlspecialchars($p['type']) ?>
              </span>
              <span class="text-muted small">ID: #<?= $p['id'] ?></span>
            </div>

            <h3 class="fw-bold text-dark mt-3 mb-1">
              <?= htmlspecialchars($p['bhk']) ?>
            </h3>
            
            <p class="text-secondary mb-4 align-items-center d-flex">
              <i class="bi bi-geo-alt-fill text-danger fs-5 me-2"></i>
              <?= htmlspecialchars($p['area']) ?>
            </p>

            <h2 class="text-success fw-bolder mb-4">
              ₹ <?= htmlspecialchars($p['price']) ?>
            </h2>

            <div class="bg-light rounded-4 p-3 mb-4">
              <div class="row gy-3">
                <div class="col-6">
                  <div class="text-muted small mb-1">Condition</div>
                  <div class="fw-semibold text-dark text-capitalize"><?= htmlspecialchars($p['property_condition']) ?></div>
                </div>
                <div class="col-6">
                  <div class="text-muted small mb-1">Availability</div>
                  <div class="fw-semibold text-dark text-capitalize"><?= htmlspecialchars($p['availability']) ?></div>
                </div>
                <div class="col-6">
                  <div class="text-muted small mb-1">BHK</div>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($p['bhk']) ?></div>
                </div>
                <div class="col-6">
                  <div class="text-muted small mb-1">Area</div>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($p['area']) ?></div>
                </div>
              </div>
            </div>

            <?php if (!empty($p['address'])) { ?>
              <div class="mb-4">
                <div class="text-muted small mb-1 fw-medium">Complete Address</div>
                <p class="mb-2 text-dark fs-6 lh-sm">
                  <?= nl2br(htmlspecialchars($p['address'])) ?>
                </p>
                <?php if (!empty($p['address_link'])) { ?>
                  <a href="<?= htmlspecialchars($p['address_link']) ?>"
                     target="_blank"
                     class="text-decoration-none text-primary fs-7 fw-medium d-inline-flex align-items-center">
                    <i class="bi bi-map-fill me-1"></i> View on Map
                  </a>
                <?php } ?>
              </div>
            <?php } ?>

            <a href="https://api.whatsapp.com/send?phone=<?= $whatsapp_number ?>&text=<?= $message ?>"
               target="_blank"
               class="btn btn-success fw-bold w-100 py-3 rounded-pill shadow-sm d-flex justify-content-center align-items-center gap-2 mb-3"
               style="font-size: 1.05rem;">
              <i class="bi bi-whatsapp fs-5"></i> Enquire Now
            </a>

            <!-- SHARE SECTION -->
            <div class="pt-3 border-top">
              <p class="text-muted small fw-bold text-uppercase mb-3">Share this Property</p>
              <div class="d-flex gap-2">
                <a href="https://api.whatsapp.com/send?text=Check out this property on Kargil Property: <?= rawurlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                   target="_blank"
                   class="btn btn-outline-success flex-grow-1 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2 small fw-semibold">
                  <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <button id="copyShareLink" 
                        class="btn btn-outline-primary flex-grow-1 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2 small fw-semibold"
                        data-url="<?= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
                  <i class="bi bi-link-45deg fs-5"></i> Copy Link
                </button>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>

<?php include "includes/footer.php"; ?>