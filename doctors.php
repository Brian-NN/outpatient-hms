<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: doctors_login.php");
    exit;


}

$doctor_name = $_SESSION['username']; 
?>
<?php

  if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: doctors_login.php");
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
            triage.TriageId, 
            triage.VisitId, 
            triage.Weight, 
            triage.Height, 
            triage.Temperature, 
            triage.HeartRate,
            triage.Status, 
            triage.RecordedAt, 
            visits.PatientId, 
            visits.CreatedAt, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS FullName, 
            patients.Gender,
            lab_requests.Test_Request_ID,
            lab_results.Test_Result_ID
        FROM triage
        INNER JOIN visits ON triage.VisitId = visits.VisitID
        INNER JOIN patients ON visits.PatientId = patients.PatientID
        LEFT JOIN lab_requests ON visits.VisitID = lab_requests.VisitId
        LEFT JOIN lab_results ON lab_requests.Test_Request_ID = lab_results.Test_Request_ID
        INNER JOIN billing ON visits.VisitID = billing.VisitID
        WHERE billing.Status = 'Processed' AND billing.Unit = 'Consultation'                

        ORDER BY visits.CreatedAt ASC";

$result = $conn->query($sql);


$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients' Queue</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .lab-btn, .diagnose-btn {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .lab-btn { background-color: blue; }
        .diagnose-btn { background-color: green; }
        .disabled { background-color: gray !important; cursor: not-allowed; }
      

    </style>
</head>
<body>
   <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="doctors.php">Home</a>
            <span style="margin-left: 15px; font-weight: bold; color: white;">Dr. <?php echo $doctor_name; ?></span>
              <form method="POST" style="display: inline-block; margin-left: 10px;">
                    <button type="submit" name="logout" style="background: none; border: none; cursor: pointer; color: red; font-size: 18px;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                    </button>
                </form>
        </div>
    </div> 
    <br><br>

<div class="container">
    
        <div class=" view_appointments row" >
            <a href="view_appointments.php"><button class="btn btn-outline-success">View Appointments</button></a>
       
            <h2>Patients' Queue</h2>

        </div>
    
    <table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Weight (kg)</th>
            <th>Height (cm)</th>
            <th>Temperature (°C)</th>
            <th>Heart Rate (bpm)</th>
            <th>Status</th>
            <th>Recorded At</th>
            <th>Actions</th>
            <th>Lab Results</th> <!-- New Column -->
        </tr>
    </thead>
    <tbody>
        <?php
       if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $disableLab = ($row['Status'] === 'sent to lab') ? "disabled" : "";
        $labResultsBtn = ($row['Test_Result_ID']) 
            ? "<a href='view_results.php?request_id={$row['Test_Request_ID']}' class='btn btn-info btn-sm'>View Results</a>" 
            : "";

        
        $statusColor = "";
        if ($row['Status'] === 'Diagnosed') {
            $statusColor = "color: green; font-weight: bold;";
        } elseif ($row['Status'] === 'sent to lab') {
            $statusColor = "color: yellow; font-weight: bold;";
        } elseif ($row['Status'] === 'Pending') {
            $statusColor = "color: red; font-weight: bold;";
        }

        echo "<tr>
            <td>{$row['FullName']}</td>
            <td>{$row['Gender']}</td>
            <td>{$row['Weight']}</td>
            <td>{$row['Height']}</td>
            <td>{$row['Temperature']}</td>
            <td>{$row['HeartRate']}</td>
            <td style='{$statusColor}'>{$row['Status']}</td>
            <td>{$row['RecordedAt']}</td>
            <td>
                <form method='POST' action='diagnose_without_test.php' style='display:inline;'>
                    <input type='hidden' name='visitId' value='{$row['VisitId']}'>
                    <button class='diagnose-btn' type='submit' name='proceed'>Diagnose</button>
                </form>

                <form method='POST' action='lab_request.php' style='display:inline;'>
                    <input type='hidden' name='visitId' value='{$row['VisitId']}'>
                    <button class='lab-btn {$disableLab}' type='submit' name='sendToLab' {$disableLab}>Lab</button>
                </form>
            </td>
            <td>{$labResultsBtn}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No records found.</td></tr>";
}
$conn->close();

        ?>
    </tbody>
</table>

</div>

</body>
</html>
