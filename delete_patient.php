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


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$patientID = $_GET['id'];


$sql = "DELETE FROM patients WHERE PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientID);

if ($stmt->execute()) {
    echo "<script>alert('Record deleted successfully!'); window.location.href = 'view_patients.php';</script>";
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
