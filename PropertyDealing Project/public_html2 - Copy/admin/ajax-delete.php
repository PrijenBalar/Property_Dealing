<?php
include "../includes/config.php";
include "../includes/auth.php";

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Select all images for physical deletion
    $imgs = mysqli_query($con, "SELECT image FROM tbl_property_images WHERE property_id=$id");
    while ($img = mysqli_fetch_assoc($imgs)) {
        @unlink("../uploads/" . $img['image']);
    }

    // Select all videos for physical deletion
    $vids = mysqli_query($con, "SELECT video FROM tbl_property_videos WHERE property_id=$id");
    while ($vid = mysqli_fetch_assoc($vids)) {
        @unlink("../uploads/" . $vid['video']);
    }

    // Delete records from database
    mysqli_query($con, "DELETE FROM tbl_property_images WHERE property_id=$id");
    mysqli_query($con, "DELETE FROM tbl_property_videos WHERE property_id=$id");
    $delete = mysqli_query($con, "DELETE FROM tbl_property WHERE id=$id");

    if ($delete) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Property Deleted Successfully ✅'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error Deleting Property'
        ]);
    }
}
?>
