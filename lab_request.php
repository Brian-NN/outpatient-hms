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


$selectedVisit = null;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitId'])) {
    $visitId = $_POST['visitId'];

    
    $updateQuery = "UPDATE triage SET Status='sent to lab' WHERE VisitId=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $visitId);
    
    if ($stmt->execute()) {
        $stmt->close(); 

        
        $fetchQuery = "SELECT 
                        triage.VisitId, 
                        visits.PatientId, 
                        CONCAT(patients.FirstName, ' ', patients.LastName) AS FullName, 
                        patients.Gender 
                    FROM triage
                    INNER JOIN visits ON visits.VisitID = triage.VisitId
                    INNER JOIN patients ON visits.PatientId = patients.PatientID
                    WHERE triage.VisitId = ?";
        
        $stmt = $conn->prepare($fetchQuery);
        $stmt->bind_param("i", $visitId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $selectedVisit = $result->fetch_assoc();
        }

        $stmt->close();
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send to Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 60%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[readonly] {
            background-color: #f1f1f1;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #21618c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
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

<?php if ($selectedVisit) { ?>
    <div class="form-container">
        <h2>Lab Request Form</h2>
        <form action="lab_request_process.php" method="POST">
            <div class="form-group">
                <label>Visit ID:</label>
                <input type="text" name="visitId" value="<?php echo htmlspecialchars($selectedVisit['VisitId']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Patient ID:</label>
                <input type="text" name="patientId" value="<?php echo htmlspecialchars($selectedVisit['PatientId']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Patient Name:</label>
                <input type="text" name="patientName" value="<?php echo htmlspecialchars($selectedVisit['FullName']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Gender:</label>
                <input type="text" name="gender" value="<?php echo htmlspecialchars($selectedVisit['Gender']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Test Type:</label>
                <select name="test_type" required>
                    <option value="Malaria Test">Malaria Test</option>
                    <option value="Typhoid Test (Widal Test)">Typhoid Test (Widal Test)</option>
                    <option value="Blood Glucose Test">Blood Glucose Test</option>
                    <option value="Urinalysis">Urinalysis</option>
                    <option value="Stool Test (Ova & Parasites)">Stool Test (Ova & Parasites)</option>
                    <option value="Full Hemogram (CBC)">Full Hemogram (CBC)</option>
                    <option value="Hepatitis B Test">Hepatitis B Test</option>
                    <option value="Blood Test">Blood Test</option>
                    <option value="MRI">MRI</option>
                    <option value="X-ray">X-ray</option>
                </select>

            </div>

            <div class="form-group">
                <label>Clinical Notes(More info):</label>
                <textarea name="clinical_notes" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Urgency:</label>
                <select name="urgency">
                    <option value="routine">Routine</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <button type="submit">Submit Lab Request</button>
        </form>
    </div>
<?php } else { ?>
    <p>No visit selected or error retrieving visit details.</p>
<?php } ?>
</body>
</html>
