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


$sql = "SELECT * FROM issued_medications";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issued Medications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
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
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #21618c;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #d4e6f1;
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
            <a href="pharmacy_records.php">Records</a>
            <a href="pharmacy_inventory.php">Inventory</a>
        </div>
    </div> 
    <br><br><br><br>

<div class="container">
    <h2>Issued Medications</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Issued ID</th>
                <th>Diagnosis ID</th>
                <th>Visit ID</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Issued At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['issued_id']) ?></td>
                    <td><?= htmlspecialchars($row['diagnosis_id']) ?></td>
                    <td><?= htmlspecialchars($row['visit_id']) ?></td>
                    <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                    <td><?= htmlspecialchars($row['treatment']) ?></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td><?= htmlspecialchars($row['issued_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No records found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
