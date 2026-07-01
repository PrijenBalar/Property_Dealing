<?php
include "../includes/config.php";

if(isset($_POST['id'])){
    $id = (int)$_POST['id'];

    $q = mysqli_query($con,"UPDATE tbl_property SET status='approved' WHERE id=$id");

    if($q){
        echo "success";
    } else {
        echo "error";
        echo mysqli_error($con); // 🔥 DEBUG
    }
}