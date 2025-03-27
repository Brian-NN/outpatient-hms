<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitId = isset($_POST['visitId']) ? intval($_POST['visitId']) : 0;
    $diagnosis = isset($_POST['diagnosis']) ? trim($_POST['diagnosis']) : '';

    
    if ($visitId == 0 || empty($diagnosis)) {
        echo "<script>alert('Error: Missing required fields.'); window.history.back();</script>";
        exit;
    }

    
    $stmt = $conn->prepare("INSERT INTO prescriptions (VisitID, diagnosis) VALUES (?, ?)");
    $stmt->bind_param("is", $visitId, $diagnosis);
    
    if ($stmt->execute()) {
        $prescriptionId = $stmt->insert_id; 
        $stmt->close();

        
        if (!empty($_POST['medicine']) && is_array($_POST['medicine'])) {
            $stmt_meds = $conn->prepare("INSERT INTO prescription_medicines (prescription_id, medicine_name, frequency, quantity) VALUES (?, ?, ?, ?)");
            $stmt2 = $conn->prepare("INSERT INTO billing (VisitID, Unit, Description, Quantity, Unit_Price, Amount) VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($_POST['medicine'] as $key => $medicineName) {
                $medicineName = trim($medicineName);
                $frequency = trim($_POST['frequency'][$key]);
                $quantity = intval($_POST['quantity'][$key]);

                if (!empty($medicineName) && !empty($frequency) && $quantity > 0) {
                    
                    
                    $checkStock = $conn->prepare("SELECT quantity FROM pharmacy_inventory WHERE ItemName = ?");
                    $checkStock->bind_param("s", $medicineName);
                    $checkStock->execute();
                    $checkStock->bind_result($availableStock);
                    $checkStock->fetch();
                    $checkStock->close();

                    if ($availableStock < $quantity) {
                        echo "<script>alert('Not enough stock for $medicineName! Available: $availableStock');</script>";
                        continue;
                    }

                    
                    $stmt_meds->bind_param("issi", $prescriptionId, $medicineName, $frequency, $quantity);
                    $stmt_meds->execute();

                   
                    $getPrice = $conn->prepare("SELECT UnitPrice FROM pharmacy_inventory WHERE ItemName = ?");
                    $getPrice->bind_param("s", $medicineName);
                    $getPrice->execute();
                    $getPrice->store_result();
                    $getPrice->bind_result($unitPrice);
                    $getPrice->fetch();
                    $getPrice->close();

                    if ($unitPrice > 0) {
                        $amount = $unitPrice * $quantity;
                        $description = "Medicine Prescription"; 

                        
                        $checkBilling = $conn->prepare("SELECT COUNT(*) FROM billing WHERE VisitID = ? AND Unit = ?");
                        $checkBilling->bind_param("is", $visitId, $medicineName);
                        $checkBilling->execute();
                        $checkBilling->bind_result($billingExists);
                        $checkBilling->fetch();
                        $checkBilling->close();

                        if ($billingExists == 0) {
                            $stmt2->bind_param("issidd", $visitId, $medicineName, $description, $quantity, $unitPrice, $amount);
                            $stmt2->execute();
                        }
                    }

                    
                    $updateInventory = $conn->prepare("UPDATE pharmacy_inventory SET quantity = quantity - ? WHERE ItemName = ?");
                    $updateInventory->bind_param("is", $quantity, $medicineName);
                    $updateInventory->execute();
                    $updateInventory->close();
                }
            }
            $stmt_meds->close();
            $stmt2->close();
        }

        $updateStatus = $conn->prepare("UPDATE visits SET Status = 'Diagnosed' WHERE VisitID = ?");
        $updateStatus->bind_param("i", $visitId);
        
        if (!$updateStatus->execute()) {
            
            $updateStatus = $conn->prepare("UPDATE appointment SET Status = 'Diagnosed' WHERE VisitId = ?");
            $updateStatus->bind_param("i", $visitId);
            $updateStatus->execute();
        }
        $updateStatus->close();

        echo "<script>alert('Submitted successfully!'); window.location.href='doctors.php';</script>";
    } else {
        echo "Error inserting prescription: " . $conn->error;
    }
}


$conn->close();
?>