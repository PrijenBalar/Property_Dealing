<?php
include "layout-header.php";
$query = "SELECT p.*, 
          (SELECT image FROM tbl_property_images WHERE property_id = p.id LIMIT 1) as main_image,
          (SELECT COUNT(*) FROM tbl_property_images WHERE property_id = p.id) as imgCount
          FROM tbl_property p 
          ORDER BY p.id DESC";
$result = mysqli_query($con, $query);
?>

<style>
.property-thumb {
    width: 40px !important;
    height: 40px !important;
    object-fit: cover !important;
    border-radius: 8px !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    transition: transform 0.2s ease;
}
.property-thumb:hover {
    transform: scale(1.1);
}
.thumb-placeholder {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    border: 1px dashed #cbd5e1;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="fw-bold mb-0 text-dark">Manage Properties</h3>
</div>

<!-- TABLE CARD -->
<div id="manageTableSection">
  <div class="table-responsive">
    <table id="managePropertyTable" class="table dashboard-table align-middle text-center w-100 text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>OWNER</th>
          <th>MOBILE</th>
          <th>TYPE</th>
          <th>BHK</th>
          <th>PREF.</th>
          <th>AREA</th>
          <th>ADDRESS</th>
          <th>MAP</th>
          <th>PRICE</th>
          <th>DESC.</th>
          <th>DATE</th>
          <th>MEDIA</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr id="row_<?= $row['id'] ?>">
          <td class="fw-bold text-nowrap" data-order="<?= $row['id'] ?>">#<?= $row['id'] ?></td>
          
          <td class="text-nowrap fw-bold text-dark"><?= htmlspecialchars($row['owner_name']) ?></td>
          
          <td class="text-nowrap small text-secondary">
              <i class="fas fa-phone-alt me-1 fs-xs"></i><?= $row['owner_mobile'] ?>
          </td>

          <td class="text-nowrap fw-medium text-dark"><?= $row['type'] ?></td>
          
          <td class="text-nowrap small text-muted"><?= $row['bhk'] ?></td>
          
          <td class="text-nowrap small text-muted"><?= !empty($row['availability']) ? htmlspecialchars($row['availability']) : '—' ?></td>

          <td class="text-nowrap fw-bold text-dark"><?= $row['area'] ?></td>

          <td class="text-start">
              <div class="text-muted small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['address']) ?></div>
          </td>

          <td>
              <?php if($row['address_link']){ ?>
              <a href="<?= $row['address_link'] ?>" target="_blank" class="text-info" data-bs-toggle="tooltip" title="View on Google Maps"><i class="fas fa-map-marker-alt"></i></a>
              <?php } else { echo '<span class="text-muted">—</span>'; } ?>
          </td>

          <td class="text-nowrap fw-bold text-success">₹ <?= $row['price'] ?></td>
          
          <td class="text-start">
              <div class="text-muted small text-truncate" style="max-width: 120px;"><?= htmlspecialchars($row['description']) ?></div>
          </td>

          <td class="text-nowrap small text-muted"><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>

          <td>
            <div class="d-flex flex-wrap gap-1 justify-content-center">
              <?php if($row['main_image']){ ?>
              <a href="../uploads/<?= $row['main_image'] ?>" target="_blank" class="media-link" data-bs-toggle="tooltip" title="View image">
                <img src="../uploads/<?= $row['main_image'] ?>" class="property-thumb" alt="Prop">
              </a>
              <?php } else { ?>
                <div class="thumb-placeholder" data-bs-toggle="tooltip" title="No image uploaded">
                  <i class="fas fa-image fs-xs"></i>
                </div>
              <?php } ?>

              <?php if($row['imgCount'] > 1){ ?>
                <button type="button" class="view-media-btn badge bg-light text-dark border p-1 rounded-2 small text-decoration-none" 
                        data-id="<?= $row['id'] ?>" data-bs-toggle="tooltip" title="See all <?= $row['imgCount'] ?> media files">
                  +<?= ($row['imgCount']-1) ?>
                </button>
              <?php } ?>
            </div>
          </td>

          <td>
            <div class="d-flex flex-column align-items-center gap-1">
              <span id="status_<?= $row['id'] ?>" class="badge <?= $row['status']=='approved' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning border-warning-subtle' ?> border px-2 py-1 rounded-pill xsmall fw-bold text-capitalize">
                <?= $row['status'] ?>
              </span>
              <div class="d-flex gap-1 justify-content-center mt-1">
                <button class="btn btn-moderation toggleStatusBtn <?= $row['status']=='approved' ? 'btn-status-pending' : 'btn-status-approve' ?>" 
                        data-id="<?= $row['id'] ?>" 
                        data-status="<?= $row['status'] ?>"
                        data-bs-toggle="tooltip" 
                        title="<?= $row['status']=='approved' ? 'Make Pending' : 'Approve Property' ?>">
                  <i class="fas <?= $row['status']=='approved' ? 'fa-clock' : 'fa-check' ?>"></i>
                </button>
                <a href="edit-property.php?id=<?= $row['id'] ?>" class="btn btn-moderation btn-edit" 
                   data-bs-toggle="tooltip" title="Edit Details">
                  <i class="fas fa-edit"></i>
                </a>
                <button class="btn btn-moderation btn-delete deletePropertyBtn" data-id="<?= $row['id'] ?>" 
                        data-bs-toggle="tooltip" title="Delete Property">
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
