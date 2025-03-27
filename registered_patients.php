
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visits Queue</title>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css.map">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.css.map">
     <link rel="stylesheet" href="bootstrap-grid.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.min.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.min.css.map">
   

    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js.map"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js.map"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="bootstrap/js/bootstrap.js.map"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js.map"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
       body {
            
            background-color: #f9f9f9;
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
 
        .table-container {
            max-width: 100%;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #21618c;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
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

        
    </div> <br><br><br><br><br>
     <div class="table-container">
        <a href="view_patients.php"><button class="btn btn-outline-success">View All Patients</button></a>

        <h2>Visits Queue</h2>
   
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

                if (isset($_POST['delete'])) {
                    $visitID = $_POST['visitID'];

                    
                    $stmt = $conn->prepare("DELETE FROM visits WHERE VisitID = ?");
                    $stmt->bind_param("i", $visitID);
                    
                    if ($stmt->execute()) {
                        echo "<p style='color: red; text-align: center;'>Visit record deleted successfully!</p>";
                        
                        header("Refresh: 2; url=" . $_SERVER['PHP_SELF']);
                    } else {
                        echo "<p style='color: red; text-align: center;'>Error deleting record!</p>";
                    }
                    
                    $stmt->close();
                }
                $sql = "SELECT visits.VisitID, visits.CreatedAt, 
                        patients.FirstName, patients.MiddleName, patients.LastName, patients.DateOfBirth, patients.Gender, 
                        patients.PhoneNumber, patients.Address, patients.Email
                        FROM visits
                        INNER JOIN patients ON visits.PatientID = patients.PatientID
                        ORDER BY visits.CreatedAt ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                     echo "<table border='1'>
                <tr>
                   
                    
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Visit Date</th>
                    <th>Action</th>
                </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                           
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($row['DateOfBirth']) . "</td>
                            <td>" . htmlspecialchars($row['Gender']) . "</td>
                            <td>" . htmlspecialchars($row['PhoneNumber']) . "</td>
                            <td>" . htmlspecialchars($row['Address']) . "</td>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['CreatedAt']) . "</td>

                             <td>
                            <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this visit record?\");'>
                                <input type='hidden' name='visitID' value='" . $row['VisitID'] . "'>
                                <button type='submit' name='delete' class='btn btn-danger'>Delete</button>

                            </form>
                        </td>
                          </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No visit records found.";
                }
                            ?>

            </tbody>
        </table>
    </div>
  
</body>
</html>