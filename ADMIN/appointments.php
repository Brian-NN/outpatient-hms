<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel_appointment'])) {
        $appointmentId = $_POST['appointment_id'];
        $sql = "UPDATE appointment SET status='Cancelled' WHERE A_Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        $stmt->close();
        $message = "Appointment #$appointmentId has been cancelled.";
    } 
    elseif (isset($_POST['proceed_appointment'])) {
        $appointmentId = $_POST['appointment_id'];
        
        
        $sql = "UPDATE appointment SET status='Confirmed' WHERE A_Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        
    
        $sql = "SELECT PatientID FROM appointment WHERE A_Id=?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $appointmentId);
        $stmt2->execute();
        $result = $stmt2->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $patientId = $row['PatientID'];
            
           
            $insertSql = "INSERT INTO visits (PatientID, A_Id, Status, CreatedAt) VALUES (?, ?, 'Active', NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ii", $patientId, $appointmentId);
            $insertStmt->execute();
            
           
            $visitId = $insertStmt->insert_id;
            $insertStmt->close();
            
           
            $unit = "Consultation"; 
            $description = "Consultation Fee"; 
            $quantity = 1; 
            $unitPrice = 1000.00;
            $amount = $unitPrice * $quantity;

            
            $billingStmt = $conn->prepare("INSERT INTO billing (VisitID, Unit, Description, Quantity, Unit_Price, Amount) VALUES (?, ?, ?, ?, ?, ?)");
            $billingStmt->bind_param("issidd", $visitId, $unit, $description, $quantity, $unitPrice, $amount);
            $billingStmt->execute();
            $billingStmt->close();
            
            $message = "Appointment #$appointmentId has been confirmed, visit created, and consultation fee billed.";
        }
        
        $stmt2->close();
        $stmt->close();
    }
}


$sql = "SELECT A_Id, Full_name, phone, Email_Address, Doctor, Date, TIME_FORMAT(Time, '%H:%i') AS Time, status FROM appointment ORDER BY Date DESC, Time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        .top-bar a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #21618c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-btn {
            display: block;
            width: 150px;
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px auto;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }
        .btn-proceed {
            background-color: #28a745;
            color: white;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-confirmed {
            color: #28a745;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
   
<div class="container">
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
 
    <h2>Appointments List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
           
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                
                $statusClass = '';
                if ($row['status'] == 'Pending') {
                    $statusClass = 'status-pending';
                } elseif ($row['status'] == 'Confirmed') {
                    $statusClass = 'status-confirmed';
                } elseif ($row['status'] == 'Cancelled') {
                    $statusClass = 'status-cancelled';
                }
                
                echo "<tr>
                        <td>{$row['A_Id']}</td>
                        <td>{$row['Full_name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['Email_Address']}</td>
                        <td>{$row['Doctor']}</td>
                        <td>{$row['Date']}</td>
                        <td>{$row['Time']}</td>
                        <td class='$statusClass'>{$row['status']}</td>
                    
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No appointments found</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>