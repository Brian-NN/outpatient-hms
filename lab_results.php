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
    $visitId = $_POST['visitId'];

    
    $updateSql = "UPDATE lab_requests SET Status = 'Test Done' WHERE VisitId = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("s", $visitId);
    $stmt->execute();
    $stmt->close();

    
    $fetchSql = "SELECT * FROM lab_requests WHERE VisitId = ?";
    $stmt = $conn->prepare($fetchSql);
    $stmt->bind_param("s", $visitId);
    $stmt->execute();
    $result = $stmt->get_result();
    $labData = $result->fetch_assoc();
    $stmt->close();
    
    
    $conn->close();

    
    if ($labData) {
?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lab Results</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
                .container { width: 50%; margin: auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
                h2 { text-align: center; color: #333; }
                label { font-weight: bold; display: block; margin-top: 10px; }
                input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
                button { background-color: #21618c; color: white; padding: 10px 15px; border: none; cursor: pointer; margin-top: 15px; width: 100%; }
                button:hover { background-color: #154360; }
            </style>
        </head>
        <body>

        <div class="container">
            <h2>Enter Lab Results</h2>
            <form method="POST" action="save_lab_results.php">
                <input type="hidden" name="Test_Request_ID" value="<?php echo htmlspecialchars($labData['Test_Request_ID']); ?>">
                <label>Patient Name:</label>
                <input type="text" name="PatientName" value="<?php echo htmlspecialchars($labData['PatientName']); ?>" readonly>

                <label>Test Type:</label>
                <input type="text" name="Test_Type" value="<?php echo htmlspecialchars($labData['Test_Type']); ?>" readonly>

                <label>Clinical Notes:</label>
                <textarea name="Clinical_Notes" readonly><?php echo htmlspecialchars($labData['Clinical_Notes']); ?></textarea>

                <label>Results:</label>
                <textarea name="Results" required></textarea>
                <label>Conclusion:</label>
                <textarea name="Conclusion" required></textarea>


                <button type="submit">Submit Results</button>
            </form>
        </div>

        </body>
        </html>
<?php
    } else {
        echo "<p>No records found for this visit.</p>";
    }
    exit(); 
}


$sql = "SELECT 
            lab_requests.Test_Request_ID, 
            lab_requests.VisitId, 
            lab_requests.PatientId, 
            lab_requests.PatientName, 
            lab_requests.Test_Type, 
            lab_requests.Clinical_Notes, 
            lab_requests.Urgency,
            lab_requests.Status, 
            visits.CreatedAt, 
            CONCAT(patients.FirstName, ' ', patients.LastName) AS FullName, 
            patients.Gender 
        FROM lab_requests
        INNER JOIN visits ON lab_requests.VisitId = visits.VisitID
        INNER JOIN patients ON visits.PatientId = patients.PatientID
        ORDER BY lab_requests.Urgency DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
        .top-bar { background-color: #21618c; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; position: fixed; width: 100%; top: 0; left: 0; height: 60px; border-bottom: 1px solid #154360; z-index: 1000; }
        .top-bar a { color: white; text-decoration: none; margin-right: 15px; }
        .container { width: 100%; margin: auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #21618c; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .diagnose-btn { padding: 5px 10px; background-color: green; color: white; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
   <div class="top-bar">
        <div>
            <span class="fw-bold">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="doctors.php">Home</a>
        </div>
    </div> 
    <br><br>

<div class="container">
    <h2>Lab Requests</h2>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Test Type</th>
                <th>Clinical Notes</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['FullName']) ?></td>
                    <td><?= htmlspecialchars($row['Gender']) ?></td>
                    <td><?= htmlspecialchars($row['Test_Type']) ?></td>
                    <td><?= htmlspecialchars($row['Clinical_Notes']) ?></td>
                    <td><?= htmlspecialchars($row['Urgency']) ?></td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="visitId" value="<?= $row['VisitId'] ?>">
                            <button class="diagnose-btn" type="submit" name="proceed">Do test</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
