<?php
$servername = "sql111.infinityfree.com";
$username = "if0_36989641";
$password = "MXiRcynLl7Xm";
$dbname = "if0_36989641_WAD_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

