<?php 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $existing_patient = isset($_POST['existing_patient']) ? $_POST['existing_patient'] : '';

    $patient_name = trim($_POST['patient_name']);
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $doctor = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    
    $nameParts = explode(' ', $patient_name, 3);
    $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
    $middleName = isset($nameParts[1]) ? $nameParts[1] : '';
    $lastName = isset($nameParts[2]) ? $nameParts[2] : '';

    if ($existing_patient == "yes") {
        
        $query = "SELECT PatientID FROM patients WHERE PhoneNumber = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        if ($patient) {
            $patient_id = $patient['PatientID'];
        } else {
            echo "<script>alert('Patient not found!'); window.history.back();</script>";
            exit;
        }

    } else {
       
        $dob = $_POST['date_of_birth'];
        $address = $_POST['address'];
        
        
        $nok_name = trim($_POST['nok_name']);
        $nokParts = explode(' ', $nok_name, 3);
        $nokFname = isset($nokParts[0]) ? $nokParts[0] : '';
        $nokMname = isset($nokParts[1]) ? $nokParts[1] : '';
        $nokLname = isset($nokParts[2]) ? $nokParts[2] : '';

        $nok_relationship = $_POST['nok_relationship'];
        $nok_contact = $_POST['nok_contact'];

        $sql = "INSERT INTO patients (FirstName, MiddleName, LastName, PhoneNumber, Email, DateOfBirth, Address, NOKFname, NOKMname, NOKLname, Relationship, NOKContact)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $firstName, $middleName, $lastName, $phone, $email, $dob, $address, $nokFname, $nokMname, $nokLname, $nok_relationship, $nok_contact);
        
        if ($stmt->execute()) {
            $patient_id = $stmt->insert_id; 
        } else {
            echo "<script>alert('Error registering new patient!'); window.history.back();</script>";
            exit;
        }
    }

    
    $sql = "INSERT INTO appointment (PatientID, Full_name, phone, Email_Address, Doctor, Date, Time)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $patient_id, $patient_name, $phone, $email, $doctor, $date, $time);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='appointment.php';</script>";
    } else {
        echo "<script>alert('Error booking appointment!'); window.history.back();</script>";
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
    <title>Book an Appointment</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style> 
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 70px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #additionalFields {
            display: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Book an Appointment</h2>
        <form action="appointment_form.php" method="POST">
            <label>Have you been treated at our facility before?</label>
            <select id="existing_patient" name="existing_patient">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label for="name">Full Name:</label>
            <input type="text" id="patient_name" name="patient_name" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email">

            <label for="doctor">Select Doctor:</label>
            <select id="doctor" name="doctor" required>
                <option value="Dr. Lisa Molly">Dr. Lisa Molly</option>
            </select>

            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="time">Select Time:</label>
            <input type="time" id="time" name="time" required>

            <div id="additionalFields">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address">

                <label for="nok_name">Next of Kin Full Name:</label>
                <input type="text" id="nok_name" name="nok_name">

                <label for="nok_relationship">Relationship:</label>
                <input type="text" id="nok_relationship" name="nok_relationship">

                <label for="nok_contact">Next of Kin Contact:</label>
                <input type="tel" id="nok_contact" name="nok_contact">
            </div>

            <button type="submit">Book Appointment</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $("#existing_patient").change(function() {
            $("#additionalFields").toggle($(this).val() === "no");
        });
    });
    </script>

</body>
</html>
