<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_GET['id'])) {
    die("Invalid request. No ID provided.");
}

$id = intval($_GET['id']); 


$sql = "DELETE FROM lab_results WHERE Test_Result_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Lab result deleted successfully!'); window.location='view_test_results.php';</script>";
} else {
    echo "<script>alert('Error deleting record.'); window.location='view_test_results.php';</script>";
}

$conn->close();
?>
