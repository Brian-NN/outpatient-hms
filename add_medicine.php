<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Entry Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .top-bar {
            background-color: #21618c;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            height: 60px;
            border-bottom: 1px solid #154360;
            z-index: 1000;
        }
        .top-bar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            width: 100%;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="pharmacy_inventory.php">Inventory</a>
        </div>
    </div> <br><br><br>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $itemName = htmlspecialchars($_POST['itemName']);
        $category = htmlspecialchars($_POST['category']);
        $quantity = htmlspecialchars($_POST['quantity']);
        $unitPrice = htmlspecialchars($_POST['unitPrice']);
        $expiryDate = htmlspecialchars($_POST['expiryDate']);
        $supplier = htmlspecialchars($_POST['supplier']);
        $batchNumber = htmlspecialchars($_POST['batchNumber']);
        $addedAt = htmlspecialchars($_POST['addedAt']);

        echo "<h2>Submitted Data:</h2>";
        echo "<p><strong>Item Name:</strong> $itemName</p>";
        echo "<p><strong>Category:</strong> $category</p>";
        echo "<p><strong>Quantity:</strong> $quantity</p>";
        echo "<p><strong>Unit Price:</strong> $unitPrice</p>";
        echo "<p><strong>Expiry Date:</strong> $expiryDate</p>";
        echo "<p><strong>Supplier:</strong> $supplier</p>";
        echo "<p><strong>Batch Number:</strong> $batchNumber</p>";
        echo "<p><strong>Added At:</strong> $addedAt</p>";
    }
    ?>

    <form method="post" action="">
        <h2 align="center">Add Medicine</h2>
        <label for="itemName">Item Name:</label>
        <input type="text" id="itemName" name="itemName" required>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="unitPrice">Unit Price:</label>
        <input type="number" id="unitPrice" name="unitPrice" step="0.01" required>

        <label for="expiryDate">Expiry Date:</label>
        <input type="date" id="expiryDate" name="expiryDate" required>

        <label for="supplier">Supplier:</label>
        <input type="text" id="supplier" name="supplier" required>

        <label for="batchNumber">Batch Number:</label>
        <input type="text" id="batchNumber" name="batchNumber" required>

        <label for="addedAt">Added At:</label>
        <input type="datetime-local" id="addedAt" name="addedAt" required>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
