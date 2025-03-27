<?php  
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: doctors_login.php");
    exit;
}
$doctor_name = $_SESSION['username']; 

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: doctors_login.php");
    exit();
}


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
            appointment.A_Id, 
            appointment.PatientID, 
            visits.VisitID,  
            CONCAT(patients.FirstName, ' ', patients.LastName) AS Full_name, 
            patients.PhoneNumber AS phone, 
            patients.Email AS Email_Address, 
            appointment.Doctor, 
            appointment.Date, 
            appointment.Time, 
            appointment.Status,
            lab_requests.Test_Request_ID,  
            lab_results.Test_Result_ID    
        FROM appointment
        INNER JOIN patients ON appointment.PatientID = patients.PatientID
        INNER JOIN visits ON appointment.A_Id = visits.A_Id
        INNER JOIN billing ON visits.VisitID = billing.VisitID
        LEFT JOIN lab_requests ON visits.VisitID = lab_requests.VisitId   
        LEFT JOIN lab_results ON lab_requests.Test_Request_ID = lab_results.Test_Request_ID  
        WHERE billing.Status = 'Processed' 
        AND billing.Unit = 'Consultation'
        ORDER BY appointment.Date ASC, appointment.Time ASC";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link href="https:scdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            max-width: 90%;
            margin: 80px auto 0;
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
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-confirmed { color: #28a745; font-weight: bold; }
        .status-cancelled { color: #dc3545; font-weight: bold; }
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
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div> 

    <div class="container">
        <h2>Appointment Records</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>A_Id</th>
                        <th>PatientID</th>
                        <th>VisitID</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Email Address</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Lab Results</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                 if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
       
        $disableLab = ($row['Status'] !== 'Confirmed') ? 'disabled' : '';

        
        $labResultsBtn = ($row['Test_Result_ID']) 
            ? "<a href='view_results.php?request_id={$row['Test_Request_ID']}' class='btn btn-info btn-sm'>View Results</a>" 
            : "";

        echo "<tr>
                <td>{$row['A_Id']}</td>
                <td>{$row['PatientID']}</td>
                <td>{$row['VisitID']}</td>
                <td>{$row['Full_name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['Email_Address']}</td>
                <td>{$row['Doctor']}</td>
                <td>{$row['Date']}</td>
                <td>{$row['Time']}</td>
                <td>{$row['Status']}</td>
                <td>
                    <form method='POST' action='diagnose_without_test2.php' style='display:inline;'>
                        <input type='hidden' name='visitId' value='{$row['VisitID']}'>
                        <button class='diagnose-btn' type='submit' name='proceed'>Diagnose</button>
                    </form>
                    <form method='POST' action='lab_request2.php' style='display:inline;'>
                        <input type='hidden' name='visitId' value='{$row['VisitID']}'>
                        <button class='lab-btn $disableLab' type='submit' name='sendToLab' $disableLab>Lab</button>
                    </form>
                    
                </td>
                <td> $labResultsBtn</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>No appointments found.</td></tr>";
}

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
