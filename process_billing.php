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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bill_ID'])) {
    $bill_ID = $_POST['bill_ID'];

    
    $conn->begin_transaction();

    try {
        
        $insertInvoice = "INSERT INTO invoice (bill_ID, created_at) VALUES (?, NOW())";
        $stmt1 = $conn->prepare($insertInvoice);
        $stmt1->bind_param("i", $bill_ID);
        $stmt1->execute();
        $stmt1->close();

        
        $updateBilling = "UPDATE billing SET Status='Processed' WHERE bill_ID=?";
        $stmt2 = $conn->prepare($updateBilling);
        $stmt2->bind_param("i", $bill_ID);
        $stmt2->execute();
        $stmt2->close();

       
        $conn->commit();
        
        echo "<script>alert('Billing record processed and added to invoice successfully!'); window.location.href='invoices.php';</script>";
    } catch (Exception $e) {
       
        $conn->rollback();
        echo "<script>alert('Error processing billing record: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}

$conn->close();
?>
