<?php
include "../includes/config.php";
include "../includes/auth.php";
?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - Kargil Property</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<link href="admin-style.css?v=<?= time() ?>" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Mobile Header -->
<div class="mobile-header">
  <div class="d-flex align-items-center gap-2">
    <i class="fa fa-building fs-5 text-info"></i>
    <span class="fw-bold">Admin Panel</span>
  </div>
  <button class="mobile-toggle" id="sidebarToggle">
    <i class="fa fa-bars fs-5"></i>
  </button>
</div>

<div class="admin-wrapper">

<!-- SIDEBAR -->
<div class="sidebar shadow-sm">
  <h4 class="mb-4">
    <i class="fa fa-building fs-5 me-2 text-white"></i>Admin
  </h4>

  <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
    <i class="fa fa-chart-line"></i> Dashboard
  </a>
  <a href="add-property.php" class="<?= $currentPage == 'add-property.php' ? 'active' : '' ?>">
    <i class="fa fa-plus-circle"></i> Add Property
  </a>
  <a href="manage-property.php" class="<?= $currentPage == 'manage-property.php' ? 'active' : '' ?>">
    <i class="fa fa-list"></i> Manage Property
  </a>
 
  <a href="logout.php" class="logout mt-auto">
    <i class="fa fa-sign-out-alt"></i> Logout
  </a>
</div>

<!-- CONTENT -->
<div class="content flex-grow-1">
