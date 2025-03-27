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


$testPrices = [
    "Malaria Test" => 500,
    "Typhoid Test (Widal Test)" => 700,
    "Blood Glucose Test" => 300,
    "Urinalysis" => 600,
    "Stool Test (Ova & Parasites)" => 800,
    "Full Hemogram (CBC)" => 1500,
    "Hepatitis B Test" => 1000,
    "Blood Test" => 900,
    "MRI" => 15000,
    "X-ray" => 2500
];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitId = $_POST['visitId'];
    $patientId = $_POST['patientId'];
    $patientName = $_POST['patientName'];
    $testType = $_POST['test_type'];
    $clinicalNotes = $_POST['clinical_notes'];
    $urgency = $_POST['urgency'];

    
    $unitPrice = isset($testPrices[$testType]) ? $testPrices[$testType] : 0;
    $quantity = 1;
    $amount = $unitPrice * $quantity;
    
    
    $insertQuery = "INSERT INTO lab_requests (VisitId, PatientId, PatientName, Test_Type, Clinical_Notes, Urgency)
                    VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iissss", $visitId, $patientId, $patientName, $testType, $clinicalNotes, $urgency);

    if ($stmt->execute()) {
        
        $unit = "Lab";
        $description = "Lab Fee - " . $testType;

        $billingQuery = "INSERT INTO billing (VisitId, Description, Unit, Quantity, Unit_Price, Amount, Status)
                         VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

        $stmtBilling = $conn->prepare($billingQuery);
        $stmtBilling->bind_param("issidd", $visitId, $description, $unit, $quantity, $unitPrice, $amount);
        
        if ($stmtBilling->execute()) {
            echo "<script>alert('Lab request and billing recorded successfully!'); window.location.href='doctors.php';</script>";
        } else {
            echo "Error submitting billing details: " . $conn->error;
        }

        $stmtBilling->close();
    } else {
        echo "Error submitting lab request: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
