<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triage Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 100%;
            width:100%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[readonly] {
            background-color: #f1f1f1;
        }
        .form-group textarea {
            resize: vertical;
            height: 100px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #21618c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>

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


$visitId = filter_input(INPUT_GET, 'visitId', FILTER_VALIDATE_INT);


$patientInfo = null;
if ($visitId > 0) {
    $sql = "SELECT visits.VisitID, visits.CreatedAt, 
                   CONCAT(patients.FirstName, ' ', patients.LastName) AS PatientName, 
                   patients.DateOfBirth, patients.Gender, patients.PhoneNumber, 
                   patients.Address, patients.Email,  visits.Status
            FROM visits
            INNER JOIN patients ON visits.PatientID = patients.PatientID
            WHERE visits.VisitID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $visitId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patientInfo = $result->fetch_assoc();
    } else {
        echo "<p style='text-align: center; color: red;'>Invalid Visit ID.</p>";
    }
    $stmt->close();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitId = filter_input(INPUT_POST, 'visitId', FILTER_VALIDATE_INT);
    $weight = filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT);
    $height = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_FLOAT);
    $temperature = filter_input(INPUT_POST, 'temperature', FILTER_VALIDATE_FLOAT);
    $heartRate = filter_input(INPUT_POST, 'heartRate', FILTER_VALIDATE_INT);
 

    
    if ($visitId && $weight !== false && $height !== false && $temperature !== false && $heartRate !== false ) {
        $sql = "INSERT INTO triage (VisitId, Weight, Height, Temperature, HeartRate) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iddds", $visitId, $weight, $height, $temperature, $heartRate); 

        if ($stmt->execute()) {
        echo "<script>alert(' submitted successfully!'); window.location.href='triage.php';</script>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='text-align: center; color: red;'>Please fill out all fields correctly.</p>";
    }
}



$conn->close();
?>

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
        
    </div> <br><br><br>

<div class="form-container">
    <h2>Triage Form</h2>
    <form method="POST">
        <input type="hidden" name="visitId" value="<?= htmlspecialchars($visitId) ?>">

        <div class="form-group">
            <label for="patientName">Patient Name</label>
            <input type="text" id="patientName" name="patientName" value="<?= htmlspecialchars($patientInfo['PatientName'] ?? '') ?>" readonly>
        </div>

        <div class="form-group">
            <label for="visitDate">Visit Date & Time</label>
            <input type="text" id="visitDate" name="visitDate" value="<?= htmlspecialchars($patientInfo['CreatedAt'] ?? '') ?>" readonly>
        </div>

        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" step="0.1" id="weight" name="weight" required>
        </div>

        <div class="form-group">
            <label for="height">Height (cm)</label>
            <input type="number" step="0.1" id="height" name="height" required>
        </div>

        <div class="form-group">
            <label for="temperature">Temperature (°C)</label>
            <input type="number" step="0.1" id="temperature" name="temperature" required>
        </div>

        <div class="form-group">
            <label for="heartRate">Heart Rate (bpm)</label>
            <input type="number" step="1" id="heartRate" name="heartRate" required>
        </div>

     

        <div class="form-group">
            <button type="submit">Submit Triage</button>
        </div>
    </form>
</div>

</body>
</html>
