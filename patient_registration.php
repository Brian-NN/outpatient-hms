<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $middlename = isset($_POST['middlename']) ? trim($_POST['middlename']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $phoneNumber = isset($_POST['phoneNumber']) ? trim($_POST['phoneNumber']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nextofkin = isset($_POST['nextofkin']) ? trim($_POST['nextofkin']) : '';
    $parts = explode(' ', $nextofkin);

    $kin_first  = isset($parts[0]) ? $parts[0] : '';
    $kin_middle = isset($parts[1]) ? $parts[1] : '';
    $kin_last   = isset($parts[2]) ? $parts[2] : '';    
    $relationship = isset($_POST['relationship']) ? trim($_POST['relationship']) : '';
    $nok_contact = isset($_POST['nok_contact']) ? trim($_POST['nok_contact']) : '';

    $stmt = $conn->prepare("INSERT INTO patients (FirstName, MiddleName, LastName, DateOfBirth, Gender, PhoneNumber, Address, Email, NOKFname, NOKMname, NOKLname, Relationship, NOKContact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $firstname, $middlename, $lastname, $dob, $gender, $phoneNumber, $address, $email,  $kin_first, $kin_middle, $kin_last, $relationship,  $nok_contact);

    if ($stmt->execute()) {
                echo "<script>alert(' Patient registered successfully... Proceed to assign visit?'); window.location.href='patient_visit.php';</script>";

    } else {
        echo "<p style='color: red; text-align: center;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Registration Form</title>
    <style>
        body {
           font-family: Arial, sans-serif;
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
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom:15px ;
        }
        .form-group label {
            display: inline;
            font-weight: bold;
            margin-bottom: 5px;
        
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color:# ;
            color: #;
            font-size: 16px;
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
            <a href="reception.php">Home</a>
            <a href="patient_registration.php">New patient</a>
            <a href="patient_visit.php">Visiting Patient</a>
            <a href="registered_patients.php">Records</a>

        </div>

        
    </div> <br> <br><br><br>
    <div class="form-container">
        <h2>Patient Registration</h2>
        <form action="patient_registration.php" method="POST">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" required>

                
            </div>
            <div class="form-group">
                <label for="middle">Middle Name</label>
                <input type="text" id="middlename" name="middlename"  required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastname" name="lastname"  required>
            </div>
            
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="tel" id="phoneNumber" name="phoneNumber"  required>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address"  required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
             <div class="form-group">
                <label for="nextofkin">Full Names of Next Of Kin </label>
                <input type="text" id="nextofkin" name="nextofkin"  required>
            </div>
            <div class="form-group">
                <label for="relationship">Relationship With Patient</label>
                <input type="text" id="relationship" name="relationship"  required>
            </div>
            
            <!-- Emergency Contact -->
            <div class="form-group">
                <label for="nok_contact">NOK Phone Number</label>
                <input type="tel" id="nok_contact" name="nok_contact"  required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Register Patient</button>
            
        </form>
        
    </div>

</body>
</html>