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


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$patientID = $_GET['id'];


$sql = "SELECT * FROM patients WHERE PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Patient not found.");
}

$patient = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['FirstName'];
    $middleName = $_POST['MiddleName'];
    $lastName = $_POST['LastName'];
    $dob = $_POST['DateOfBirth'];
    $gender = $_POST['Gender'];
    $phone = $_POST['PhoneNumber'];
    $address = $_POST['Address'];
    $email = $_POST['Email'];
    $nokFname = $_POST['NOKFname'];
    $nokMname = $_POST['NOKMname'];
    $nokLname = $_POST['NOKLname'];
    $relationship = $_POST['Relationship'];
    $nokContact = $_POST['NOKContact'];

    $updateSQL = "UPDATE patients SET 
        FirstName=?, MiddleName=?, LastName=?, DateOfBirth=?, Gender=?, PhoneNumber=?, 
        Address=?, Email=?, NOKFname=?, NOKMname=?, NOKLname=?, Relationship=?, NOKContact=? 
        WHERE PatientID=?";
    
    $stmt = $conn->prepare($updateSQL);
    $stmt->bind_param("sssssssssssssi", $firstName, $middleName, $lastName, $dob, $gender, $phone, 
                      $address, $email, $nokFname, $nokMname, $nokLname, $relationship, $nokContact, $patientID);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href = 'view_patients.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
        .container { max-width: 500px; background: white; padding: 20px; margin: 50px auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #21618c; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        button:hover { opacity: 0.8; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Patient</h2>
    <form method="POST">
        <input type="text" name="FirstName" value="<?= $patient['FirstName'] ?>" required>
        <input type="text" name="MiddleName" value="<?= $patient['MiddleName'] ?>">
        <input type="text" name="LastName" value="<?= $patient['LastName'] ?>" required>
        <input type="date" name="DateOfBirth" value="<?= $patient['DateOfBirth'] ?>" required>
        <select name="Gender">
            <option value="Male" <?= $patient['Gender'] == "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= $patient['Gender'] == "Female" ? "selected" : "" ?>>Female</option>
        </select>
        <input type="text" name="PhoneNumber" value="<?= $patient['PhoneNumber'] ?>" required>
        <input type="text" name="Address" value="<?= $patient['Address'] ?>" required>
        <input type="email" name="Email" value="<?= $patient['Email'] ?>" required>
        <input type="text" name="NOKFname" value="<?= $patient['NOKFname'] ?>" required>
        <input type="text" name="NOKMname" value="<?= $patient['NOKMname'] ?>">
        <input type="text" name="NOKLname" value="<?= $patient['NOKLname'] ?>" required>
        <input type="text" name="Relationship" value="<?= $patient['Relationship'] ?>" required>
        <input type="text" name="NOKContact" value="<?= $patient['NOKContact'] ?>" required>
        <button type="submit">Update Patient</button>
    </form>
</div>

</body>
</html>
