<?php
include "../includes/config.php";
include "../includes/auth.php";

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = (int)$_POST['id'];
    $type = $_POST['type']; // 'image' or 'video'

    if ($type === 'image') {
        $res = mysqli_query($con, "SELECT image FROM tbl_property_images WHERE id=$id");
        if ($row = mysqli_fetch_assoc($res)) {
            @unlink("../uploads/" . $row['image']);
            mysqli_query($con, "DELETE FROM tbl_property_images WHERE id=$id");
            echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        }
    } elseif ($type === 'video') {
        $res = mysqli_query($con, "SELECT video FROM tbl_property_videos WHERE id=$id");
        if ($row = mysqli_fetch_assoc($res)) {
            @unlink("../uploads/" . $row['video']);
            mysqli_query($con, "DELETE FROM tbl_property_videos WHERE id=$id");
            echo json_encode(['status' => 'success', 'message' => 'Video deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Video not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid media type']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing ID or type']);
}
?>
