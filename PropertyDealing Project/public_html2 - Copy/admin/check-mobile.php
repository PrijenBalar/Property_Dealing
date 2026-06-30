<?php
include "../includes/config.php";

$mobile = trim($_POST['mobile'] ?? '');

if ($mobile !== '' && preg_match('/^[0-9]{10}$/', $mobile)) {

    $q = mysqli_query(
        $con,
        "SELECT id FROM tbl_property WHERE owner_mobile='$mobile' LIMIT 1"
    );

    if (mysqli_num_rows($q) > 0) {
        echo "exists";
    } else {
        echo "ok";
    }
}
