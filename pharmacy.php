<?php  
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Pharmacy') {
    header("Location: pharmacy_login.php");
    exit;
}


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: pharmacy_login.php");
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['issue_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    $update_sql = "UPDATE prescription_medicines SET status = 'Issued' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $medicine_id);
    
    if ($stmt->execute()) {
        $success_message = "Medicine issued successfully!";
    } else {
        $error_message = "Error issuing medicine: " . $conn->error;
    }
    $stmt->close();
}

$pharmacist_name = $_SESSION['username'];


$sql = "
SELECT 
    pm.id AS medicine_id,
    pr.id AS prescription_id,
    pm.medicine_name,
    pm.frequency,
    pm.quantity AS prescribed_quantity,
    pm.created_at,
    pm.status,
    v.VisitID,
    p.PatientID,
    CONCAT(p.FirstName, ' ', p.LastName) AS patient_name,
    p.gender
FROM prescription_medicines pm
JOIN prescriptions pr ON pm.prescription_id = pr.id
JOIN visits v ON pr.VisitID = v.VisitID
JOIN patients p ON v.PatientID = p.PatientID
JOIN (
    SELECT DISTINCT VisitID 
    FROM billing 
    WHERE Status = 'Processed' 
    AND Description = 'Medicine Prescription'
) b ON v.VisitID = b.VisitID
ORDER BY p.PatientID, pm.created_at DESC
";

$result = $conn->query($sql);


$patients = [];
while ($row = $result->fetch_assoc()) {
    $patient_id = $row['PatientID'];
    if (!isset($patients[$patient_id])) {
        $patients[$patient_id] = [
            'patient_info' => [
                'PatientID' => $row['PatientID'],
                'patient_name' => $row['patient_name'],
                'gender' => $row['gender'],
                'VisitID' => $row['VisitID']
            ],
            'medicines' => []
        ];
    }
    $patients[$patient_id]['medicines'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 80px 20px 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .top-bar {
            background-color: #21618c;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 1560px;
            top: 0;
            left: 0;
            height: 60px;
            z-index: 1000;
        }
        .top-bar a{
            text-decoration: none;
            color: white;
            margin-right: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status-pending {
            color: red;
            font-weight: bold;
        }
        .status-issued {
            color: green;
            font-weight: bold;
        }
        .issue-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .issue-btn:hover {
            background-color: #45a049;
        }
        .issued-text {
            color: #666;
            font-style: italic;
        }
        .patient-row {
            background-color: #f2f2f2 !important;
            font-weight: bold;
        }
        .medicine-row {
            background-color: white;
        }
        .no-border-top {
            border-top: none !important;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <span>JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="pharmacy.php">Home</a>
            <a href="pharmacy_inventory.php">Inventory</a>
            <span style="margin-left: 15px; font-weight: bold;"><?php echo $pharmacist_name; ?></span>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" style="background: red; border: none; cursor: pointer; color: white;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                </button>
            </form>
        </div>
    </div>

    <h2>Pharmacy Dashboard - Prescriptions</h2>
    
    <?php if (isset($success_message)): ?>
        <div style="color: green; padding: 10px; background: #e6ffe6; margin-bottom: 15px;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div style="color: red; padding: 10px; background: #ffebeb; margin-bottom: 15px;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Gender</th>
                <th>Medicine</th>
                <th>Frequency</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient_id => $patient_data): ?>
                    <?php $first_row = true; ?>
                    <?php foreach ($patient_data['medicines'] as $index => $medicine): ?>
                        <tr class="<?php echo $first_row ? 'patient-row' : 'medicine-row'; ?>">
                            <?php if ($first_row): ?>
                                <td rowspan="<?php echo count($patient_data['medicines']); ?>">
                                    <?php echo htmlspecialchars($patient_data['patient_info']['patient_name']); ?>
                                </td>
                                <td rowspan="<?php echo count($patient_data['medicines']); ?>">
                                    <?php echo htmlspecialchars($patient_data['patient_info']['gender']); ?>
                                </td>
                                <?php $first_row = false; ?>
                            <?php endif; ?>
                            
                            <td><?php echo htmlspecialchars($medicine['medicine_name']); ?></td>
                            <td><?php echo htmlspecialchars($medicine['frequency']); ?></td>
                            <td><?php echo htmlspecialchars($medicine['prescribed_quantity']); ?></td>
                            <td class="status-<?php echo strtolower($medicine['status']); ?>">
                                <?php echo htmlspecialchars($medicine['status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($medicine['created_at']); ?></td>
                            <td>
                                <?php if ($medicine['status'] !== 'Issued'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">
                                        <button type="submit" name="issue_medicine" class="issue-btn">Issue</button>
                                    </form>
                                <?php else: ?>
                                    <span class="issued-text">Issued</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No prescriptions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>