<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Billing Officer') {
    header("Location: billing_login.php");
    exit;
}


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: billing_login.php");
    exit();
}

$billing_officer_name = $_SESSION['username'];
?>

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
            billing.bill_ID,
            billing.VisitID,
            billing.Unit,
            billing.Description,
            billing.Quantity,
            billing.Unit_Price,
            billing.Amount,
            visits.PatientID,
            visits.CreatedAt,
            billing.Status,
            CONCAT(patients.FirstName, ' ', patients.LastName) AS PatientName,
            patients.Gender,
            patients.PhoneNumber
        FROM billing
        JOIN visits ON billing.VisitID = visits.VisitID
        JOIN patients ON visits.PatientID = patients.PatientID
        ORDER BY visits.PatientID, billing.bill_ID DESC";

$result = $conn->query($sql);


$patients = [];
while ($row = $result->fetch_assoc()) {
    $patient_id = $row['PatientID'];
    if (!isset($patients[$patient_id])) {
        $patients[$patient_id] = [
            'patient_info' => [
                'PatientID' => $row['PatientID'],
                'PatientName' => $row['PatientName'],
                'Gender' => $row['Gender'],
                'PhoneNumber' => $row['PhoneNumber'],
                'CreatedAt' => $row['CreatedAt']
            ],
            'billing_items' => []
        ];
    }
    $patients[$patient_id]['billing_items'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billing Records</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
            margin-top: 80px;
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
            background-color: white;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        .patient-row {
            background-color: white !important;
            font-weight: bold;
        }
        .billing-row {
            background-color: white;
        }
        .no-border-top {
            border-top: none !important;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-processed {
            color: green;
            font-weight: bold;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
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
            <span style="margin-left: 15px; font-weight: bold; color: white;">Mr. <?php echo $billing_officer_name; ?></span>
            <form method="POST" style="display: inline-block; margin-left: 10px;">
                <button type="submit" name="logout" style="background: red; border: none; cursor: pointer; color: white; font-size: 18px;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div> 
    <br><br>

    <h2>Billing Records</h2>

    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Contact</th>
                <th>Visit Date</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient_id => $patient_data): ?>
                    <?php $first_row = true; ?>
                    <?php foreach ($patient_data['billing_items'] as $index => $item): ?>
                        <tr class="<?php echo $first_row ? 'patient-row' : 'billing-row'; ?>">
                            <?php if ($first_row): ?>
                                <td rowspan="<?php echo count($patient_data['billing_items']); ?>">
                                    <?php echo htmlspecialchars($patient_data['patient_info']['PatientName']); ?><br>
                                    <small>ID: <?php echo htmlspecialchars($patient_data['patient_info']['PatientID']); ?></small>
                                </td>
                                <td rowspan="<?php echo count($patient_data['billing_items']); ?>">
                                    <?php echo htmlspecialchars($patient_data['patient_info']['PhoneNumber']); ?>
                                </td>
                                <td rowspan="<?php echo count($patient_data['billing_items']); ?>">
                                    <?php echo htmlspecialchars($patient_data['patient_info']['CreatedAt']); ?>
                                </td>
                                <?php $first_row = false; ?>
                            <?php endif; ?>
                            
                            <td><?php echo htmlspecialchars($item['Description']); ?></td>
                            <td><?php echo htmlspecialchars($item['Unit']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($item['Unit_Price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars(number_format($item['Amount'], 2)); ?></td>
                            <td class="status-<?php echo strtolower($item['Status']); ?>">
                                <?php echo htmlspecialchars($item['Status']); ?>
                            </td>
                            <td>
                                <?php if ($item['Status'] !== 'Processed'): ?>
                                    <form method='POST' action='process_billing.php'>
                                        <input type='hidden' name='bill_ID' value='<?php echo htmlspecialchars($item['bill_ID']); ?>'>
                                        <button type='submit' class='btn btn-outline-success btn-sm'>Process</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center;">No billing records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>