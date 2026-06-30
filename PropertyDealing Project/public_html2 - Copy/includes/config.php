<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//$con = mysqli_connect("localhost","u165416594_kargil","Kargil7383","u165416594_kargilproperty");
$con = mysqli_connect("localhost","root","root","kargil_property1");

if(!$con){
    die("Database connection failed");
}
