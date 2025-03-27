<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Lab Technician') {
    header("Location: lab_login.php");
    exit;


}

$doctor_name = $_SESSION['username'];
?>
<?php

  if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: lab_login.php");
    exit();
}
?>
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

$sql = "SELECT Test_Result_ID, Test_Request_ID, PatientName, Test_Type, Clinical_Notes, Results, Conclusion, CreatedAt FROM lab_results ORDER BY CreatedAt DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
          .top-bar {
            background-color: #21618c;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 1560px;
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
            max-width: 1200px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #21618c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #21618c;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.pass {
            color: green;
        }
        .status.fail {
            color: red;
        }
        .btn-container {
            display: flex;
            gap: 8px;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-edit {
            background-color: #f39c12;
            color: white;
        }
        .btn-edit:hover {
            background-color: #e67e22;
            transform: scale(1.05);
        }
        .btn-delete {
            background-color: #c0392b;
            color: white;
        }
        .btn-delete:hover {
            background-color: #a93226;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
   <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="lab.php">Home</a>
             <a href="view_test_results.php">Test Results</a>
             <span style="margin-left: 15px; font-weight: bold; color: white;">Mr. <?php echo $doctor_name; ?></span>
              <form method="POST" style="display: inline-block; margin-left: 10px;">
                    <button type="submit" name="logout" style="background: none; border: none; cursor: pointer; color: red; font-size: 18px;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                    </button>
                </form>
        </div>
    </div> 
    <br><br>
    <div class="table-container">
        <h2>Lab Results</h2>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>
                    <th>Result ID</th>
                    <th>Request ID</th>
                    <th>Patient Name</th>
                    <th>Test Type</th>
                    <th>Clinical Notes</th>
                    <th>Results</th>
                    <th>Conclusion</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                  </tr>";

            while ($row = $result->fetch_assoc()) {
                $statusClass = strtolower(trim($row['Conclusion'])) === "pass" ? "pass" : "fail";

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Test_Result_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Test_Request_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Test_Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Clinical_Notes']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Results']) . "</td>";
                echo "<td class='status $statusClass'>" . htmlspecialchars($row['Conclusion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
                echo "<td>
                        <div class='btn-container'>
                            <a href='edit_lab_result.php?id=" . $row['Test_Result_ID'] . "' class='btn btn-edit'> Edit</a>
                            <a href='delete_lab_result.php?id=" . $row['Test_Result_ID'] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                        </div>
                      </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p style='text-align: center; color: gray;'>No lab results found.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>
