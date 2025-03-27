<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if (isset($_POST['medicine']) && isset($_POST['quantity'])) {
    $medicine = trim($_POST['medicine']);
    $requested_quantity = (int) $_POST['quantity'];

    
    $stmt = $conn->prepare("SELECT Quantity FROM pharmacy_inventory WHERE ItemName = ?");
    $stmt->bind_param("s", $medicine);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $available_quantity = $row ? (int) $row['Quantity'] : 0;

    if ($requested_quantity > $available_quantity) {
        echo json_encode(["status" => "insufficient", "message" => "Not enough stock!", "available" => $available_quantity]);
    } else {
        echo json_encode(["status" => "sufficient", "message" => "Stock available"]);
    }
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
