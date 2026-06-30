<?php
include "../includes/config.php";
include "../includes/auth.php";

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $images = [];
    $videos = [];

    // Fetch Images
    $imgRes = mysqli_query($con, "SELECT image FROM tbl_property_images WHERE property_id=$id");
    while ($row = mysqli_fetch_assoc($imgRes)) {
        $images[] = $row['image'];
    }

    // Fetch Videos
    $vidRes = mysqli_query($con, "SELECT video FROM tbl_property_videos WHERE property_id=$id");
    while ($row = mysqli_fetch_assoc($vidRes)) {
        $videos[] = $row['video'];
    }

    echo json_encode([
        'status' => 'success',
        'id' => $id,
        'images' => $images,
        'videos' => $videos
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid ID'
    ]);
}
?>
