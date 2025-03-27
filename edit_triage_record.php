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


if (isset($_GET['id'])) {
    $triageId = $_GET['id'];
    
   
    $sql = "SELECT * FROM triage WHERE TriageId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $triageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $triage = $result->fetch_assoc();
} else {
    die("Invalid request");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $temperature = $_POST['temperature'];
    $heartRate = $_POST['heart_rate'];
    
    $updateSql = "UPDATE triage SET Weight = ?, Height = ?, Temperature = ?, HeartRate = ? WHERE TriageId = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("dddii", $weight, $height, $temperature, $heartRate, $triageId);
    
    if ($updateStmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='triage_list.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Triage Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 500px;
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
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .cancel-btn {
            background-color: #6c757d;
        }
        .cancel-btn:hover {
            background-color: #5a6268;
        }
        a {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Triage Record</h2>
        <form method="POST">
            <label>Weight (kg):</label>
            <input type="text" name="weight" value="<?php echo $triage['Weight']; ?>" required>
            
            <label>Height (cm):</label>
            <input type="text" name="height" value="<?php echo $triage['Height']; ?>" required>
            
            <label>Temperature (°C):</label>
            <input type="text" name="temperature" value="<?php echo $triage['Temperature']; ?>" required>
            
            <label>Heart Rate (bpm):</label>
            <input type="text" name="heart_rate" value="<?php echo $triage['HeartRate']; ?>" required>
            
            <div class="buttons">
                <button type="submit">Update</button>
               
                <a href="triage_list.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
