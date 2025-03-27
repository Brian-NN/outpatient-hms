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


$sql = "SELECT 
            invoice.invoice_ID, 
            invoice.bill_ID, 
            invoice.created_at, 
            billing.VisitID, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS PatientName,
            billing.Description, 
            billing.Amount
        FROM invoice
        JOIN billing ON invoice.bill_ID = billing.bill_ID
        JOIN visits ON billing.VisitID = visits.VisitID
        JOIN patients ON visits.PatientID = patients.PatientID
        ORDER BY billing.VisitID, billing.Description, invoice.invoice_ID DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Records</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        .download-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }
        .download-btn:hover {
            background-color: #218838;
        }
        .group-row {
            background-color: #f8f9fa;
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
            <a href="billing.php">Home</a>
            <a href="invoices.php">Invoices</a>
        </div>
    </div> 
    <br><br>

<h2>Invoice Records</h2>

<table>
    <tr>
        <th>Invoice ID</th>
        <th>Bill ID</th>
        <th>Visit ID</th>
        <th>Patient Name</th>
        <th>Description</th>
        <th>Amount</th>
        <th>Created At</th>
        <th>Download</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        $currentVisitID = null;
        $medicinePrescriptions = [];
        
        while ($row = $result->fetch_assoc()) {
           
            if ($currentVisitID !== $row['VisitID'] && !empty($medicinePrescriptions)) {
                
                echo "<tr class='group-row'>";
                echo "<td>" . implode("<br>", array_column($medicinePrescriptions, 'invoice_ID')) . "</td>";
                echo "<td>" . implode("<br>", array_column($medicinePrescriptions, 'bill_ID')) . "</td>";
                echo "<td>" . $medicinePrescriptions[0]['VisitID'] . "</td>";
                echo "<td>" . $medicinePrescriptions[0]['PatientName'] . "</td>";
                echo "<td>Medicine Prescription</td>";
                echo "<td>" . number_format(array_sum(array_column($medicinePrescriptions, 'Amount')), 2) . "</td>";
                echo "<td>" . $medicinePrescriptions[0]['created_at'] . "</td>";
                echo "<td><a href='download_invoice.php?invoice_id=" . implode(",", array_column($medicinePrescriptions, 'invoice_ID')) . "' class='download-btn' target='_blank'>Download All</a></td>";
                echo "</tr>";
                
                $medicinePrescriptions = [];
            }
            
           
            if ($currentVisitID !== $row['VisitID']) {
                $currentVisitID = $row['VisitID'];
            }
            
            
            if ($row['Description'] === 'Medicine Prescription') {
                $medicinePrescriptions[] = $row;
            } else {
               
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['invoice_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['bill_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['VisitID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                echo "<td>" . htmlspecialchars(number_format($row['Amount'], 2)) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "<td><a href='download_invoice.php?invoice_id=" . $row['invoice_ID'] . "' class='download-btn' target='_blank'>Download</a></td>";
                echo "</tr>";
            }
        }
        
        
        if (!empty($medicinePrescriptions)) {
            echo "<tr class='group-row'>";
            echo "<td>" . implode("<br>", array_column($medicinePrescriptions, 'invoice_ID')) . "</td>";
            echo "<td>" . implode("<br>", array_column($medicinePrescriptions, 'bill_ID')) . "</td>";
            echo "<td>" . $medicinePrescriptions[0]['VisitID'] . "</td>";
            echo "<td>" . $medicinePrescriptions[0]['PatientName'] . "</td>";
            echo "<td>Medicine Prescription</td>";
            echo "<td>" . number_format(array_sum(array_column($medicinePrescriptions, 'Amount')), 2) . "</td>";
            echo "<td>" . $medicinePrescriptions[0]['created_at'] . "</td>";
            echo "<td><a href='download_invoice.php?invoice_id=" . implode(",", array_column($medicinePrescriptions, 'invoice_ID')) . "' class='download-btn' target='_blank'>Download All</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' style='text-align: center;'>No invoices found.</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>