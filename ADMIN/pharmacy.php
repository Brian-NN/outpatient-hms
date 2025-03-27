<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$issued_quantities = [];
$sql_issued = "SELECT item_name, SUM(Quantity) as total_issued FROM issued_medications GROUP BY item_name";
$result_issued = $conn->query($sql_issued);

if ($result_issued->num_rows > 0) {
    while ($row = $result_issued->fetch_assoc()) {
        $issued_quantities[$row['item_name']] = $row['total_issued'];
    }
}


$sql_inventory = "SELECT * FROM pharmacy_inventory";
$result_inventory = $conn->query($sql_inventory);

if ($result_inventory->num_rows > 0) {
    while ($row = $result_inventory->fetch_assoc()) {
        $itemName = $row['ItemName'];  
        $availableStock = $row['Quantity'];
        $issuedStock = isset($issued_quantities[$itemName]) ? $issued_quantities[$itemName] : 0;
        $remainingStock = max($availableStock - $issuedStock, 0); 

       
        $update_sql = "UPDATE pharmacy_inventory SET Quantity = ? WHERE ItemName = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("is", $remainingStock, $itemName);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
     
        h2 {
            color: #333;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 100%;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>


    <div class="">
        <h2>Pharmacy Inventory</h2>

        <?php
       
        $sql_inventory = "SELECT * FROM pharmacy_inventory";
        $result_inventory = $conn->query($sql_inventory);

        if ($result_inventory->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Issued</th>
                        <th>Remaining Stock</th>
                        <th>Unit Price</th>
                        <th>Expiry Date</th>
                        <th>Supplier</th>
                        <th>Batch Number</th>
                        <th>Added At</th>
                    </tr>";

            while ($row = $result_inventory->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['ItemName']}</td>
                        <td>{$row['Category']}</td>
                        <td>{$row['Quantity']}</td>
                        <td>" . (isset($issued_quantities[$row['ItemName']]) ? $issued_quantities[$row['ItemName']] : 0) . "</td>
                        <td>{$row['Quantity']}</td>
                        <td>Ksh {$row['UnitPrice']}</td>
                        <td>{$row['ExpiryDate']}</td>
                        <td>{$row['Supplier']}</td>
                        <td>{$row['BatchNumber']}</td>
                        <td>{$row['AddedAt']}</td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }

        $conn->close();
        ?>

    </div>

</body>
</html>
