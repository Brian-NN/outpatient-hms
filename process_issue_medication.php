<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $diagnosisID = intval($_POST['DiagnosisID']);
    $visitID     = intval($_POST['VisitID']);
    $itemName    = trim($_POST['ItemName']);
    $quantity    = trim($_POST['quantity']);

    
    if (empty($itemName)) {
        echo "Item Name is required.";
        exit();
    }

    
    $sql = "SELECT Diagnosis, Treatment FROM diagnoses WHERE DiagnosisID = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $diagnosisID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $diagnosis = $row['Diagnosis'];
        $treatment = $row['Treatment'];
    } else {
        echo "Diagnosis record not found.";
        exit();
    }
    $stmt->close();

    
    $insertSQL = "INSERT INTO issued_medications (diagnosis_id, visit_id, diagnosis, treatment, item_name, quantity) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSQL);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iissss", $diagnosisID, $visitID, $diagnosis, $treatment, $itemName, $quantity );

    if ($stmt->execute()) {
        header("Location: pharmacy.php?success=Medication issued successfully");
        exit();
    } else {
        echo "Error executing query: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
