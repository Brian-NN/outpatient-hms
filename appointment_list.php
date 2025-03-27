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
    $patient_name = $_POST['patient_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $doctor = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
   
    
    $sql = "INSERT INTO appointment (Full_name, phone, Email_Address, Doctor, Date, Time)
            VALUES (?, ?, ?, ?, ? , ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $patient_name, $phone, $email,$doctor,  $date, $time,);

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
    <style> 
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
         .top-bar {
            background-color: #21618c;
            color: white;
            width: 1550px;
            height: 60px;
            position: fixed;
            border-bottom:2px solid #154360;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            z-index: 1000;
            top: 0;
            left: 0;
        }
        .top-bar a {
            color: white;
            text-decoration: none;

        }
        .top-bar a:hover {
            text-decoration: underline;
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
    </style>
</head>
<body>
     <!-- Code for topbar -->
    <div class="top-bar">
        <div>
            <span class="fw-bold"> JAMII COMMUNITY HOSPITAL</span> 
        </div>
        <div>
            <a href="appointment_list.php">View appointments</a>
        </div>
    </div>
    <div class="container">
        <h2>Book an Appointment</h2>
        <form action="appointment.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="patient_name" name="patient_name" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email">

            <label for="doctor">Select Doctor:</label>
            <select id="doctor" name="doctor" required>
                <option value="Dr. Lisa Molly">Dr. Lisa Molly </option>
                
            </select>

            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="time">Select Time:</label>
            <input type="time" id="time" name="time" required>

            <button type="submit">Book Appointment</button>
        </form>
    </div>
</body>
</html>



