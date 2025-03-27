<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['item_name'])) {
    $item_name = $_GET['item_name'];
    $sql = "SELECT * FROM pharmacy_inventory WHERE ItemName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $item_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $medicine = $result->fetch_assoc();
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_quantity = $_POST['quantity'];
    $additional_quantity = $_POST['additional_quantity'];
    $total_quantity = $new_quantity + $additional_quantity;
    $category = $_POST['category'];
    $unit_price = $_POST['unit_price'];
    $expiry_date = $_POST['expiry_date'];
    $supplier = $_POST['supplier'];
    $batch_number = $_POST['batch_number'];
    
    $update_sql = "UPDATE pharmacy_inventory SET Quantity = ?, Category = ?, UnitPrice = ?, ExpiryDate = ?, Supplier = ?, BatchNumber = ? WHERE ItemName = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("issssss", $total_quantity, $category, $unit_price, $expiry_date, $supplier, $batch_number, $item_name);
    
    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location='pharmacy_inventory.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%;
            background-color: #007bff;
            border: none;
        }
        .btn-secondary {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Medicine</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" value="<?php echo $medicine['ItemName']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="<?php echo $medicine['Category']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo $medicine['Quantity']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Additional Quantity</label>
                <input type="number" name="additional_quantity" class="form-control" value="0" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Unit Price (Ksh)</label>
                <input type="text" name="unit_price" class="form-control" value="<?php echo $medicine['UnitPrice']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" value="<?php echo $medicine['ExpiryDate']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Supplier</label>
                <input type="text" name="supplier" class="form-control" value="<?php echo $medicine['Supplier']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Batch Number</label>
                <input type="text" name="batch_number" class="form-control" value="<?php echo $medicine['BatchNumber']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="pharmacy_inventory.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>