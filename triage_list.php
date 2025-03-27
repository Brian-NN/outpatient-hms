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
            triage.RecordedAt, 
            visits.PatientId, 
            visits.CreatedAt, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS FullName, 
            patients.Gender 
        FROM triage
        INNER JOIN visits ON triage.VisitId = visits.VisitID
        INNER JOIN patients ON visits.PatientId = patients.PatientID
        ORDER BY visits.CreatedAt ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triage Records</title>
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
            width:100% ;
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
    </style>
</head>
<body>
   <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="triage.php">Home</a>
            <a href="triage_form.php">Vitals Form</a>
            <a href="triage_list.php">Triage Records</a>
            


        </div>
        
    </div> <br><br>

<div class="container">
    <h2>Triage Records</h2>

<table>
    <thead>
        <tr>
            <th>Triage ID</th>
            <th>Visit ID</th>
            <th>Patient ID</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Weight (kg)</th>
            <th>Height (cm)</th>
            <th>Temperature (°C)</th>
            <th>Heart Rate (bpm)</th>
            <th>Recorded At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['TriageId']}</td>
                    <td>{$row['VisitId']}</td>
                    <td>{$row['PatientId']}</td>
                    <td>{$row['FullName']}</td>
                    <td>{$row['Gender']}</td>
                    <td>{$row['Weight']}</td>
                    <td>{$row['Height']}</td>
                    <td>{$row['Temperature']}</td>
                    <td>{$row['HeartRate']}</td>
                    <td>{$row['RecordedAt']}</td>
                    <td>
                        <a href='edit_triage_record.php?id={$row['TriageId']}' style='text-decoration:none; padding:5px 10px; background:#f39c12; color:white; border-radius:5px;'>Edit</a>
                        <a href='delete_triage_record.php?id={$row['TriageId']}' onclick='return confirm(\"Are you sure you want to delete this record?\")' style='text-decoration:none; padding:5px 10px; background:#e74c3c; color:white; border-radius:5px;'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No records found.</td></tr>";
        }
        $conn->close();
        ?>
    </tbody>
</table>

</div>

</body>
</html>
