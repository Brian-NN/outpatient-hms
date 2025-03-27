<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Lab Technician') {
    header("Location: lab_login.php");
    exit;


}

$doctor_name = $_SESSION['username']; 
?>
<?php

  if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: lab_login.php");
    exit();
}
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
            lab_requests.Test_Request_ID, 
            lab_requests.VisitId, 
            lab_requests.PatientId, 
            lab_requests.PatientName, 
            lab_requests.Test_Type, 
            lab_requests.Clinical_Notes, 
            lab_requests.Urgency,
            lab_requests.Status, 
            visits.CreatedAt, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS FullName, 
            patients.Gender, billing.VisitID, billing.Status 
        FROM lab_requests
        INNER JOIN visits ON lab_requests.VisitId = visits.VisitID
        INNER JOIN patients ON visits.PatientId = patients.PatientID
        JOIN billing ON visits.VisitID = billing.VisitID
        WHERE billing.Status = 'Processed' AND billing.Unit = 'Lab'
        ORDER BY lab_requests.Urgency DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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
            width: 1560px;
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
            width: 100%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #21618c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .diagnose-btn {
            padding: 5px 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
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
            <a href="lab.php">Home</a>
             <a href="view_test_results.php">Test Results</a>
             <span style="margin-left: 15px; font-weight: bold; color: white;">Mr. <?php echo $doctor_name; ?></span>
              <form method="POST" style="display: inline-block; margin-left: 10px;">
                    <button type="submit" name="logout" style="background: none; border: none; cursor: pointer; color: red; font-size: 18px;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                    </button>
                </form>
        </div>
    </div> 
    <br><br>

<div class="container">
    <h2>Lab Requests</h2>

    <table>
        <thead>
            <tr>
              
                <th>Full Name</th>
                <th>Gender</th>
                <th>Test Type</th>
                <th>Clinical Notes</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            $statusColor = ($row['Status'] === 'Processed') ? "color: green; font-weight: bold;" : 
                           (($row['Status'] === 'Pending') ? "color: red; font-weight: bold;" : "");

            echo "<tr>
                <td>{$row['FullName']}</td>
                <td>{$row['Gender']}</td>
                <td>{$row['Test_Type']}</td>
                <td>{$row['Clinical_Notes']}</td>
                <td>{$row['Urgency']}</td>
                <td style='{$statusColor}'>{$row['Status']}</td>                       
                <td>
                    <form method='POST' action='lab_results.php' style='display:inline;'>
                        <input type='hidden' name='visitId' value='{$row['VisitId']}'>
                        <button class='diagnose-btn' type='submit' name='proceed'>Do test</button>
                    </form>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No lab requests today.</td></tr>";
    }
    $conn->close();
    ?>
</tbody>

    </table>
</div>

</body>
</html>
