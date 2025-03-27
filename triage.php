<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Triage Nurse') {
    header("Location: triage_login.php");
    exit;

}



$doctor_name = $_SESSION['username']; 
?>
<?php
   
  if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: triage_login.php");
    exit();
}
 ?>   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Visits</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: white;
             padding-top: 60px;

    }
       .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    position: fixed;
    width: 1550px;
    top: 0;
    left: 0;
    background-color: #21618c;
    color: white;
    height: 60px;
    z-index: 1000;
    flex-wrap: wrap; 
}
.top-bar div {
    display: flex;
    align-items: center;
    gap: 15px; 
    flex-wrap: wrap;
}

        .top-bar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .table-container {
            max-width: 1100px;
            margin: -200px auto 0;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body>
   <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="triage.php">Home</a>
            <a href="triage_form.php">Vitals Form</a>
            <a href="triage_list.php">Triage Records</a>
             <span style="margin-left: 15px; font-weight: bold; color: white;">Mr. <?php echo $doctor_name; ?></span>
              <form method="POST" style="display: inline-block; margin-left: 10px;">
                    <button type="submit" name="logout" style="background: none; border: none; cursor: pointer; color: red; font-size: 18px;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                    </button>
                </form>

        </div>
    </div>

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
            die("Connection failed: " . $conn->connect_error);
        }

       
      
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed'])) { 
    if (!empty($_POST['visitId']) && is_numeric($_POST['visitId'])) {
        $visitId = $_POST['visitId'];

        
        $updateSql = "UPDATE visits SET Status = 'Attended' WHERE VisitID = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("i", $visitId);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            
            header("Location: triage_form.php?visitId=" . urlencode($visitId));
            exit();
        } else {
            echo "<p style='text-align: center; color: red;'>Error updating status.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='text-align: center; color: red;'>Invalid Visit ID.</p>";
    }
}


       
        $sql = "SELECT visits.VisitID, visits.CreatedAt, 
                       patients.FirstName, patients.MiddleName, patients.LastName, patients.DateOfBirth, patients.Gender, 
                       patients.PhoneNumber, patients.Address, patients.Email, visits.Status AS visit_status
                FROM visits
                INNER JOIN patients ON visits.PatientID = patients.PatientID
                INNER JOIN billing ON visits.VisitID = billing.VisitID
                WHERE billing.Status = 'Processed' AND billing.Unit = 'Consultation'                
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
                    <th>Proceed</th>
                </tr>";

           
            while ($row = $result->fetch_assoc()) {
                
                $status = strtolower(trim($row['visit_status']));
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['VisitID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['FirstName'] . " " . $row['LastName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PhoneNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
                echo "<td class='status " . ($status == 'attended' ? "attended'>Attended" : "pending'>Pending") . "</td>";
                echo "<td>
                        <form method='POST'>
                           <input type='hidden' name='visitId' value='" . $row['VisitID'] . "'>
                            <button class='proceed-btn' type='submit' name='proceed' value='Proceed'>Proceed</button>
                        </form>
                      </td>";
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
