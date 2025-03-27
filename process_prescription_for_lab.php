<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "jamii-hms";


error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}


$visitId = isset($_POST['visitId']) ? intval($_POST['visitId']) : 0;
$testRequestId = isset($_POST['testRequestId']) ? intval($_POST['testRequestId']) : 0;
$diagnosis = isset($_POST['diagnosis']) ? htmlspecialchars(trim($_POST['diagnosis']), ENT_QUOTES, 'UTF-8') : '';


if ($visitId == 0 || empty($diagnosis)) {
    die("Error: Missing required fields.");
}


$stmt = $conn->prepare("INSERT INTO prescriptions (VisitID, diagnosis, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $visitId, $diagnosis);

if ($stmt->execute()) {
    $prescriptionId = $stmt->insert_id;
    $stmt->close();

    $warnings = [];

    
    if (!empty($_POST['medicine']) && is_array($_POST['medicine'])) {
        $stmt_meds = $conn->prepare("INSERT INTO prescription_medicines 
            (prescription_id, Test_Request_ID, medicine_name, frequency, quantity, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())");
        
        $stmt_billing = $conn->prepare("INSERT INTO billing (VisitID, Unit, Description, Quantity, Unit_Price, Amount) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($_POST['medicine'] as $index => $medicineName) {
            $medicineName = trim($medicineName);
            $frequency = trim($_POST['frequency'][$index]);
            $quantity = intval($_POST['quantity'][$index]);

            if (!empty($medicineName) && !empty($frequency) && $quantity > 0) {
                $stmt_meds->bind_param("iissi", $prescriptionId, $testRequestId, $medicineName, $frequency, $quantity);
                $stmt_meds->execute();
                
                
                $getPrice = $conn->prepare("SELECT UnitPrice FROM pharmacy_inventory WHERE ItemName = ?");
                $getPrice->bind_param("s", $medicineName);
                $getPrice->execute();
                $getPrice->bind_result($unitPrice);
                $getPrice->fetch();
                $getPrice->close();
                
                if ($unitPrice > 0) {
                    $amount = $unitPrice * $quantity;
                    $description = "Medicine Prescription";
                    
                   
                    $stmt_billing->bind_param("issidd", $visitId, $medicineName, $description, $quantity, $unitPrice, $amount);
                    $stmt_billing->execute();
                }

               
                $updateInventory = $conn->prepare("UPDATE pharmacy_inventory 
                    SET quantity = quantity - ? 
                    WHERE ItemName = ? AND quantity >= ?");
                $updateInventory->bind_param("isi", $quantity, $medicineName, $quantity);
                $updateInventory->execute();

                if ($updateInventory->affected_rows == 0) {
                    $warnings[] = "Not enough stock for $medicineName!";
                }
                $updateInventory->close();
            }
        }
        $stmt_meds->close();
        $stmt_billing->close();
    }

    
    $updateTriage = $conn->prepare("UPDATE triage SET Status = 'Diagnosed' WHERE VisitID = ?");
    $updateTriage->bind_param("i", $visitId);
    $updateTriage->execute();
    $updateTriage->close();

   
    if (!empty($warnings)) {
        $warningMessage = implode("\\n", $warnings);
        echo "<script>alert('$warningMessage');</script>";
    }

   
    header("Location: doctors.php");
    exit();
} else {
    error_log("Error inserting prescription: " . $conn->error);
    die("Error inserting prescription.");
}

$conn->close();
?>
