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


if (!isset($_GET['id'])) {
    die("Invalid request. No ID provided.");
}

$id = intval($_GET['id']); // Sanitize input


$sql = "SELECT * FROM lab_results WHERE Test_Result_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Lab result not found.");
}

$row = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientName = $_POST['PatientName'];
    $testType = $_POST['Test_Type'];
    $clinicalNotes = $_POST['Clinical_Notes'];
    $results = $_POST['Results'];
    $conclusion = $_POST['Conclusion'];

    
    $updateSql = "UPDATE lab_results SET PatientName=?, Test_Type=?, Clinical_Notes=?, Results=?, Conclusion=? WHERE Test_Result_ID=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssi", $patientName, $testType, $clinicalNotes, $results, $conclusion, $id);

    if ($updateStmt->execute()) {
        echo "<script>alert('Lab result updated successfully!'); window.location='view_test_results.php';</script>";
    } else {
        echo "<script>alert('Error updating record.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lab Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #21618c;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #21618c;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #1b4f72;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Lab Result</h2>
    <form method="POST">
        <label>Patient Name:</label>
        <input type="text" name="PatientName" value="<?= htmlspecialchars($row['PatientName']) ?>" required>

        <label>Test Type:</label>
        <input type="text" name="Test_Type" value="<?= htmlspecialchars($row['Test_Type']) ?>" required>

        <label>Clinical Notes:</label>
        <textarea name="Clinical_Notes" required><?= htmlspecialchars($row['Clinical_Notes']) ?></textarea>

        <label>Results:</label>
        <textarea name="Results" required><?= htmlspecialchars($row['Results']) ?></textarea>

        <label>Conclusion:</label>
        <input type="text" name="Conclusion" value="<?= htmlspecialchars($row['Conclusion']) ?>" required>

        <button type="submit" class="btn">Update Result</button>
    </form>
</div>

</body>
</html>
