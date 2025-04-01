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


$searchQuery = "";
$result = null;

if (isset($_POST['search'])) {
    $searchQuery = trim($_POST['search']);

    if (!empty($searchQuery)) {
        $stmt = $conn->prepare("SELECT * FROM patients WHERE FirstName LIKE ? OR LastName LIKE ? OR PhoneNumber LIKE ? OR Email LIKE ?");
        $searchTerm = "%{$searchQuery}%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}


if (isset($_POST['assign_visit'])) {
    $patientId = $_POST['patient_id'];

    
    $visitId = uniqid('VST_');

    
    $stmt = $conn->prepare("INSERT INTO visits (PatientID) VALUES (?)");
    $stmt->bind_param("i", $patientId);

    if ($stmt->execute()) {
        $visitId = $conn->insert_id; 

        
        $unit = "Consultation"; 
        $description = "Consultation Fee"; 
        $quantity = 1; 
        $unitPrice = 1000.00;
        $amount = $unitPrice * $quantity;

        
        $stmt2 = $conn->prepare("INSERT INTO billing (VisitID, Unit, Description, Quantity, Unit_Price, Amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("issidd", $visitId, $unit, $description, $quantity, $unitPrice, $amount);

        if ($stmt2->execute()) {
            echo "<script>alert('Saved successfully!'); window.location.href='reception.php';</script>";
        } else {
            echo "Error inserting into Billing table: " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        echo "Error inserting into Visits table: " . $stmt->error;
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reception</title>
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
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
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
        .button-container {
            text-align: center;
            
        }
        .btn-lg {
            width: 250px;
            height: 100px;
            margin: 20px;
        }
                table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            display: <?php echo ($result !== null) ? 'table' : 'none'; ?>;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;

        }
        th {
            background-color: #21618c;
            color: white;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
        }
        button {
            padding: 10px;
            background-color: #21618c;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 12px;
        }
        button:hover {
            background-color: #4CBB17;
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
            <a href="patient_registration.php">New patient</a>
            <a href="patient_visit.php">Visiting Patient</a>
            <a href="registered_patients.php">Records</a>

        </div>
        
    </div> <br><br><br><br><br>
    <h2>Search Patients</h2>
    
    <form method="POST">
        <input type="text" name="search" placeholder="Search by Name, Phone, or Email" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($result !== null): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Email</th>
                <th>Action</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['PatientID']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['MiddleName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($row['DateOfBirth']) . "</td>
                            <td>" . htmlspecialchars($row['Gender']) . "</td>
                            <td>" . htmlspecialchars($row['PhoneNumber']) . "</td>
                            <td>" . htmlspecialchars($row['Address']) . "</td>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>
                                <form method='POST' style='margin: 0;'>
                                    <input type='hidden' name='patient_id' value='" . htmlspecialchars($row['PatientID']) . "'>
                                    <button type='submit' name='assign_visit'>Select</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='10' style='text-align: center;'>No matching patients found.</td></tr>";
            }
            ?>
        </table>
    <?php endif; ?>


</body>
</html>
