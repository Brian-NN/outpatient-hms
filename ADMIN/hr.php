<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Records</title>
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
        .table-container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding-top: 100px;
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
    </style>
</head>
<body>

    <div class="table-container">
        <h2 style="text-align: center;">Employee Records</h2>

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

        
        $sql = "SELECT EmployeeID, FirstName, MiddleName, LastName, DateOfBirth, Gender, PhoneNumber, Email, Address, Position, Salary, HireDate, CreatedAt FROM human_resource ORDER BY CreatedAt DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Hire Date</th>
                    <th>Created At</th>
                </tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['FirstName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['MiddleName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['LastName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PhoneNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Position']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Salary']) . "</td>";
                echo "<td>" . htmlspecialchars($row['HireDate']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p style='text-align: center; color: gray;'>No employee records found.</p>";
        }

       
        $conn->close();
        ?>
    </div>

</body>
</html>
