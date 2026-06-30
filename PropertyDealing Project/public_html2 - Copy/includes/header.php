
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kargil Property</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/index.css?v=<?= time() ?>">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script defer src="assets/js/index.js"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <!-- LOGO -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/kargil_property_logo.png" style="height:40px" class="me-2">
      <span class="fw-bold text-primary fs-5">Kargil Property</span>
    </a>

    <!-- Mobile Toggle -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible Content -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <div class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
        <a href="index.php" class="nav-link fw-medium">Home</a>
        <a href="contact.php" class="nav-link fw-medium">Contact Us</a>
        
        <?php if (!isset($hideFilter)) : ?>
          <button id="filterToggle" class="btn btn-primary rounded-pill px-4" data-bs-toggle="collapse" data-bs-target="#mainFilterBar">
            <i class="bi bi-funnel-fill me-1"></i> Filter
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
