<?php include "layout-header.php"; ?>

<?php
$totalProperty = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM tbl_property"))[0];
$pending       = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM tbl_property WHERE status='pending'"))[0];
$approved      = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM tbl_property WHERE status='approved'"))[0];
?>

<h3 class="mb-4 fw-bold">Dashboard</h3>

<div class="row g-3">

<div class="col-md-4">
  <div class="stat-card bg-blue">
    <p>Total Property</p>
    <h2><?= $totalProperty ?></h2>
  </div>
</div>

<div class="col-md-4">
  <div class="stat-card bg-orange" id="pendingCard" style="cursor:pointer;" data-bs-toggle="tooltip" title="Click to view pending properties">
    <p>Pending Approval</p>
    <h2 id="pendingCount"><?= $pending ?></h2>
  </div>
</div>

<div class="col-md-4">
  <div class="stat-card bg-green">
    <p>Approved Properties</p>
    <h2><?= $approved ?></h2>
  </div>
</div>

</div>

<!-- TABLE -->
<div id="pendingTableSection" style="display:none;">

<h5 class="fw-bold mb-3 mt-4">Pending Properties</h5>

<div class="table-responsive">

<table id="propertyTable" class="table dashboard-table align-middle text-center w-100 text-nowrap">
<thead>
<tr>
<th>ID</th>
<th>Contact</th>
<th>Property Info</th>
<th>Location</th>
<th>Price & Details</th>
<th>Media</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php
$data = mysqli_query($con,"SELECT * FROM tbl_property WHERE status='pending' ORDER BY id DESC");

while($row = mysqli_fetch_assoc($data)){
?>

<tr id="row_<?= $row['id'] ?>">

<td class="fw-bold text-nowrap" data-order="<?= $row['id'] ?>">#<?= $row['id'] ?></td>

<td class="text-start">
    <div class="fw-bold text-dark"><?= htmlspecialchars($row['owner_name']) ?></div>
    <div class="text-secondary small"><i class="fas fa-phone-alt me-1 fs-xs"></i><?= $row['owner_mobile'] ?></div>
</td>

<td class="text-start">
    <div class="fw-medium"><?= $row['type'] ?></div>
    <div class="text-muted small"><?= $row['bhk'] ?> • <?= $row['availability'] ?></div>
</td>

<td class="text-start">
    <div class="fw-medium text-dark"><?= $row['area'] ?></div>
    <div class="text-muted small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['address']) ?></div>
    <?php if($row['address_link']){ ?>
    <a href="<?= $row['address_link'] ?>" target="_blank" class="text-info xsmall" data-bs-toggle="tooltip" title="View Maps"><i class="fas fa-map-marker-alt me-1"></i>View Map</a>
    <?php } ?>
</td>

<td class="text-start">
    <div class="fw-bold text-success">₹ <?= $row['price'] ?></div>
    <div class="text-muted small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['description']) ?></div>
</td>

<td>
  <div class="d-flex flex-wrap gap-1 justify-content-center">
    <?php
    $imgs = mysqli_query($con,"SELECT image FROM tbl_property_images WHERE property_id=".$row['id']." LIMIT 2");
    $imgCount = mysqli_num_rows(mysqli_query($con,"SELECT id FROM tbl_property_images WHERE property_id=".$row['id']));
    while($img=mysqli_fetch_assoc($imgs)){
    ?>
    <a href="../uploads/<?= $img['image'] ?>" target="_blank" class="media-link" data-bs-toggle="tooltip" title="View Image">
      <img src="../uploads/<?= $img['image'] ?>" class="property-thumb" alt="Property">
    </a>
    <?php } ?>
    <?php if($imgCount > 2){ ?>
      <button type="button" class="badge bg-light text-dark border p-1 rounded-2 small text-decoration-none view-media-btn" 
              data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="View all <?= $imgCount ?> photos">
        +<?= ($imgCount-2) ?>
      </button>
    <?php } ?>
  </div>
</td>

<td>
<div class="d-flex flex-column align-items-center gap-1">
  <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill xsmall fw-bold">Pending</span>
  <div class="d-flex gap-2 justify-content-center">
    <button class="btn btn-moderation btn-approve approveBtn" data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Approve Property">
      <i class="fas fa-check"></i>
    </button>
    <button class="btn btn-moderation btn-delete deletePropertyBtn" data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="Delete Permanently">
      <i class="fas fa-trash-alt"></i>
    </button>
  </div>
</div>
</td>

</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>

<?php include "layout-footer.php"; ?>