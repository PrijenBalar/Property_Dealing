<?php
include "../includes/config.php";

$sql = "SELECT * FROM tbl_property WHERE status='approved'";

if($_POST['type']!="")
  $sql.=" AND type='".$_POST['type']."'";

if($_POST['bhk']!="")
  $sql.=" AND bhk='".$_POST['bhk']."'";

if($_POST['min']!="")
  $sql.=" AND price>=".$_POST['min'];

if($_POST['max']!="")
  $sql.=" AND price<=".$_POST['max'];

$q = mysqli_query($con,$sql);

while($row=mysqli_fetch_assoc($q)){
?>
<div class="col-12 col-md-4 mb-3">
  <div class="card shadow">
    <img src="uploads/<?= $row['image'] ?>" class="card-img-top">
    <div class="card-body">
      <h6><?= $row['title'] ?></h6>
      <p>₹ <?= $row['price'] ?></p>
    </div>
  </div>
</div>
<?php } ?>
