<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: doctors_login.php");
    exit;
}


if (!isset($_GET['TriageId']) || empty($_GET['TriageId'])) {
    header("Location: triage_records.php?error=no_record_selected");
    exit;
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$triageId = $_GET['TriageId'];
$sql = "SELECT * FROM Triage WHERE TriageId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $triageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: triage_records.php?error=record_not_found");
    exit;
}

$row = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $triageId = $_POST['triageId'];
    $visitId = $_POST['visitId'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $temperature = $_POST['temperature'];
    $heartRate = $_POST['heartRate'];
    $symptoms = $_POST['symptoms'];
    $assignedDoctor = $_POST['assignedDoctor'];
    $requiredTest = $_POST['required_test'];

   
    $sql = "INSERT INTO lab (TriageId, VisitId, Weight, Height, Temperature, HeartRate, Symptoms, Assigned_Doctor, Required_Test)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iisssssss",
        $triageId,
        $visitId,
        $weight,
        $height,
        $temperature,
        $heartRate,
        $symptoms,
        $assignedDoctor,
        $requiredTest
    );

  if ($stmt->execute()) {
    
    header("Location: doctors.php?success=record_sent");
    exit;
} else {
    echo "Error: " . $conn->error;
}


    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Triage Record</title>
     <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 0.5rem;
        }

        input, textarea, button {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        input:focus, textarea:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            input, textarea, button {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Triage Record</h2>
        <form action="sent_to_lab.php?TriageId=<?php echo htmlspecialchars($row['TriageId']); ?>" method="POST">
            <label for="triageId">Triage ID</label>
            <input type="text" id="triageId" name="triageId" value="<?php echo htmlspecialchars($row['TriageId']); ?>" readonly>

            <label for="visitId">Visit ID</label>
            <input type="text" id="visitId" name="visitId" value="<?php echo htmlspecialchars($row['VisitId']); ?>" readonly>

            <label for="weight">Weight (kg)</label>
            <input type="number" step="0.1" id="weight" name="weight" value="<?php echo htmlspecialchars($row['Weight']); ?>" required>

            <label for="height">Height (cm)</label>
            <input type="number" step="0.1" id="height" name="height" value="<?php echo htmlspecialchars($row['Height']); ?>" required>

            <label for="temperature">Temperature (°C)</label>
            <input type="number" step="0.1" id="temperature" name="temperature" value="<?php echo htmlspecialchars($row['Temperature']); ?>" required>

            <label for="heartRate">Heart Rate (bpm)</label>
            <input type="number" id="heartRate" name="heartRate" value="<?php echo htmlspecialchars($row['HeartRate']); ?>" required>

            <label for="symptoms">Symptoms</label>
            <textarea id="symptoms" name="symptoms" rows="4" required><?php echo htmlspecialchars($row['Symptoms']); ?></textarea>

            <label for="assignedDoctor">Assigned Doctor</label>
            <input type="text" id="assignedDoctor" name="assignedDoctor" value="<?php echo htmlspecialchars($row['Assigned_Doctor']); ?>" required>

            <label for="required_test">Required Test</label>
            <input type="text" id="required_test" name="required_test" placeholder="Enter required test" required>

            <button type="submit">Send to Lab</button>
        </form>
    </div>
</body>
</html>
