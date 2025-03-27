<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Visits</title>
    <style>
            body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: white;
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
        .table-container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding-top: 400px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #21618c;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .proceed-btn {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .proceed-btn:hover {
            background-color: #218838;
        }
        .status {
            font-weight: bold;
        }
        .status.attended {
            color: green;
        }
        .status.pending {
            color: red;
        }
        /* Navbar styles */
        .navbar {
            background-color: #007bff;
            overflow: hidden;
            padding: 10px 20px;
        }
        .navbar a {
            float: left;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
        }
        .navbar a:hover {
            background-color: #0056b3;
            border-radius: 4px;
        }
        .navbar a.active {
            background-color: #0056b3;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <h2 style="text-align: center;">Patient Visit Records</h2>
        
        <?php
       
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "jamii-hms";
        
        $conn = new mysqli($servername, $username, $password, $dbname);

        
        if ($conn->connect_error) {
            die("Connection failed.");
        }

       
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed'])) { 
            $visitId = $_POST['visitId'];
            $updateSql = "UPDATE visits SET Status = 'Attended' WHERE VisitID = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("i", $visitId);
            
            if ($stmt->execute()) {
                
                header("Location: triage_form.php?visitId=" . $visitId);
                exit();
            } else {
                echo "<p style='text-align: center; color: red;'>Error updating status.</p>";
            }
            
            $stmt->close();
        }

        
        $sql = "SELECT visits.VisitID, visits.CreatedAt, 
                       patients.FirstName, patients.MiddleName, patients.LastName, patients.DateOfBirth, patients.Gender, 
                       patients.PhoneNumber, patients.Address, patients.Email, visits.Status
                FROM visits
                INNER JOIN patients ON visits.PatientID = patients.PatientID
                ORDER BY visits.CreatedAt ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>
                    <th>Visit ID</th>
                    <th>Patient Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Email</th>
                 
                    <th>Visit Date</th>
                    <th>Status</th>
                </tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['VisitID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['FirstName'] . " " . $row['LastName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PhoneNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
                echo "<td class='status " . 
                     ($row['Status'] === 'Attended' ? "attended'>Attended" : "pending'>Pending") . 
                     "</td>";
               
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p style='text-align: center; color: gray;'>No records found.</p>";
        }

        
        $conn->close();
        ?>
    </div>

</body>
</html>
