<?php
include "../includes/config.php";
include "../includes/auth.php";

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Get current status
    $res = mysqli_query($con, "SELECT status FROM tbl_property WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    $currentStatus = $row['status'];

    // Toggle status
    $newStatus = ($currentStatus === 'approved') ? 'pending' : 'approved';
    $update = mysqli_query($con, "UPDATE tbl_property SET status='$newStatus' WHERE id=$id");

    if ($update) {
        echo json_encode([
            'status' => 'success',
            'new_status' => $newStatus,
            'message' => 'Status Updated Successfully ✅'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error Updating Status'
        ]);
    }
}
?>
