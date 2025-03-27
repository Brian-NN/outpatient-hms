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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $testRequestId = $_POST['Test_Request_ID'];
    $patientName = $_POST['PatientName'];
    $testType = $_POST['Test_Type'];
    $clinicalNotes = $_POST['Clinical_Notes'];
    $results = $_POST['Results'];
    $conclusion = $_POST['Conclusion'];

    
    $sql = "INSERT INTO lab_results (Test_Request_ID, PatientName, Test_Type, Clinical_Notes, Results, Conclusion) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isssss", $testRequestId, $patientName, $testType, $clinicalNotes, $results, $conclusion);
        
        if ($stmt->execute()) {
            echo "<script>alert('Results submitted successfully!'); window.location.href='lab.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>
