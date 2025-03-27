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


if (!isset($_GET['invoice_id']) || empty($_GET['invoice_id'])) {
    die("Invalid request.");
}

$invoice_ids = explode(',', $_GET['invoice_id']);


$valid_ids = [];
foreach ($invoice_ids as $id) {
    $id = intval(trim($id));
    if ($id > 0) {
        $valid_ids[] = $id;
    }
}

if (empty($valid_ids)) {
    die("Invalid invoice IDs.");
}


$placeholders = implode(',', array_fill(0, count($valid_ids), '?'));
$sql = "SELECT 
            invoice.invoice_ID, 
            invoice.bill_ID, 
            invoice.created_at, 
            billing.VisitID,
            billing.Unit,
            billing.Quantity,
            billing.Unit_Price, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS PatientName,
            billing.Description, 
            billing.Amount
        FROM invoice
        JOIN billing ON invoice.bill_ID = billing.bill_ID
        JOIN visits ON billing.VisitID = visits.VisitID
        JOIN patients ON visits.PatientID = patients.PatientID
        WHERE invoice.invoice_ID IN ($placeholders)";

$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($valid_ids));
$stmt->bind_param($types, ...$valid_ids);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No invoices found.");
}

$invoices = $result->fetch_all(MYSQLI_ASSOC);
$total_amount = array_sum(array_column($invoices, 'Amount'));
$first_invoice = $invoices[0];
$created_at = min(array_column($invoices, 'created_at'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #<?php echo implode(',', $valid_ids); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
        .invoice-box { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; }
        h2 { color: #21618c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #21618c; color: white; }
        .total { font-weight: bold; font-size: 1.2em; }
        .print-btn { margin-top: 20px; padding: 10px 20px; background: #21618c; color: white; border: none; cursor: pointer; }
        .print-btn:hover { background: #154360; }
        .item-row { border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

    <div class="invoice-box">
        <h2>JAMII COMMUNITY HOSPITAL</h2>
        <p><strong>Invoice IDs:</strong> <?php echo implode(', ', $valid_ids); ?></p>
        <p><strong>Patient:</strong> <?php echo $first_invoice['PatientName']; ?></p>
        <p><strong>Visit ID:</strong> <?php echo $first_invoice['VisitID']; ?></p>
        <p><strong>Date Issued:</strong> <?php echo $created_at; ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit_Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                <tr class="item-row">
                    <td><?php echo htmlspecialchars($invoice['Unit']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['Description']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['Quantity']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['Unit_Price']); ?></td>
                    <td>KES <?php echo number_format($invoice['Amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td>Total Amount</td>
                    <td>KES <?php echo number_format($total_amount, 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <hr>
        <p style="font-size: 12px;">Thank you for choosing Jamii Community Hospital</p>
        <button class="print-btn" onclick="window.print()">Print Invoice</button>
    </div>

</body>
</html>

<?php
$conn->close();
?>