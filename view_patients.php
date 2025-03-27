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


$sql = "SELECT PatientID, FirstName, MiddleName, LastName, DateOfBirth, Gender, PhoneNumber, 
               Address, Email, NOKFname, NOKMname, NOKLname, Relationship, NOKContact, CreatedAt 
        FROM patients ORDER BY CreatedAt DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            max-width: 1200px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #21618c;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #21618c;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-edit {
            background-color: #ffc107;
            color: black;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
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

        
    </div> <br> <br>
    <div class="container">
        <h2>Patient Records</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Patient ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>NOK First Name</th>
                    <th>NOK Middle Name</th>
                    <th>NOK Last Name</th>
                    <th>Relationship</th>
                    <th>NOK Contact</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['PatientID']}</td>
                                <td>{$row['FirstName']}</td>
                                <td>{$row['MiddleName']}</td>
                                <td>{$row['LastName']}</td>
                                <td>{$row['DateOfBirth']}</td>
                                <td>{$row['Gender']}</td>
                                <td>{$row['PhoneNumber']}</td>
                                <td>{$row['Address']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['NOKFname']}</td>
                                <td>{$row['NOKMname']}</td>
                                <td>{$row['NOKLname']}</td>
                                <td>{$row['Relationship']}</td>
                                <td>{$row['NOKContact']}</td>
                                <td>{$row['CreatedAt']}</td>
                                <td>
                                    <button class='btn btn-edit' onclick='editPatient({$row['PatientID']})'>Edit</button>
                                    <button class='btn btn-delete' onclick='confirmDelete({$row['PatientID']})'>Delete</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='16' style='text-align:center;'>No patients found</td></tr>";
                }

                $conn->close();
                ?>

            </table>
        </div>
    </div>

    <script>
        function editPatient(patientID) {
            window.location.href = "edit_patient.php?id=" + patientID;
        }

        function confirmDelete(patientID) {
            if (confirm("Are you sure you want to delete this record?")) {
                window.location.href = "delete_patient.php?id=" + patientID;
            }
        }
    </script>

</body>S
</html>
