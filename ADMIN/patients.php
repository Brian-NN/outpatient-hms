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


$sql = "SELECT PatientID, FirstName, MiddleName, LastName, DateOfBirth, Gender, PhoneNumber, Address, Email, NOKFname, NOKMname, NOKLname, Relationship, NOKContact, CreatedAt FROM patients";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f6f7;
            font-family: Arial, sans-serif;
        }

        .container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #21618c;
            font-weight: bold;
            border-bottom: 4px solid #154360;
            display: block;
            text-align: center;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        .table thead {
            background-color: #21618c;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f0f8ff;
            transition: 0.3s;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px;
        }

        .table th {
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .table td {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Patient Records</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
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
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['PatientID'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['FirstName'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['MiddleName'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['LastName'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['DateOfBirth'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['Gender'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['PhoneNumber'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['Address'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['Email'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['NOKFname'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['NOKMname'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['NOKLname'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['Relationship'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['NOKContact'], ENT_QUOTES, 'UTF-8') . "</td>
                                <td>" . htmlspecialchars($row['CreatedAt'], ENT_QUOTES, 'UTF-8') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='15' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
