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
$error = null;
$success = null;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitId'])) {
    $visitId = (int) $_POST['visitId'];

    try {
        
        error_log("Processing visit ID: $visitId");

        
        $getAppointmentQuery = "SELECT A_Id FROM visits WHERE VisitID = ?";
        $stmt = $conn->prepare($getAppointmentQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $visitId);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("No appointment found for visit ID: $visitId");
        }
        
        $row = $result->fetch_assoc();
        $a_id = $row['A_Id'];
        $stmt->close();

        
        error_log("Found A_Id: $a_id");

       
        $updateQuery = "UPDATE appointment SET Status='sent to lab' WHERE A_Id=?";
        $stmt = $conn->prepare($updateQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $a_id);

        if (!$stmt->execute()) {
            throw new Exception("Update failed: " . $stmt->error);
        }
        
        $stmt->close();
        $success = "Appointment status updated successfully";

        
        $fetchQuery = "SELECT 
                v.VisitID, 
                v.PatientID,
                CONCAT(p.FirstName, ' ', p.LastName) AS FullName, 
                p.Gender,
                p.DateOfBirth,
                p.PhoneNumber
            FROM visits v
            INNER JOIN patients p ON v.PatientID = p.PatientID
            WHERE v.VisitID = ?";
        
        $stmt = $conn->prepare($fetchQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $visitId);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $selectedVisit = $result->fetch_assoc();
         
            error_log("Fetched visit details: " . print_r($selectedVisit, true));
        } else {
            throw new Exception("Visit details not found for ID: $visitId");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Error: " . $e->getMessage());
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
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #21618c;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-control[readonly] {
            background-color: #f8f9fa;
        }
        .btn-primary {
            background-color: #21618c;
            border-color: #1a5276;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #1a5276;
        }
        .top-bar {
            background-color: #21618c;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            height: 60px;
            z-index: 1000;
        }
        .top-bar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 16px;
        }
        .alert {
            margin: 20px auto;
            max-width: 800px;
        }
        .patient-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .patient-info h4 {
            color: #21618c;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <span style="font-weight: bold; font-size: 18px;">JAMII COMMUNITY HOSPITAL</span>
        </div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="doctors.php">Home</a>
        </div>
    </div> 
    
    <div style="height: 80px;"></div> <!-- Spacer for fixed header -->

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($selectedVisit): ?>
        <div class="form-container">
            <h2>Lab Request Form</h2>
            
            <div class="patient-info">
                <h4>Patient Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($selectedVisit['PatientID']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($selectedVisit['FullName']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($selectedVisit['Gender']); ?></p>
                        <p><strong>Visit ID:</strong> <?php echo htmlspecialchars($selectedVisit['VisitID']); ?></p>
                    </div>
                </div>
            </div>

            <form action="lab_request_process.php" method="POST">
                <input type="hidden" name="visitId" value="<?php echo htmlspecialchars($selectedVisit['VisitID']); ?>">
                <input type="hidden" name="patientId" value="<?php echo htmlspecialchars($selectedVisit['PatientID']); ?>">

                <div class="form-group">
                    <label>Patient Name:</label>
                    <input type="text" class="form-control" name="patientName" value="<?php echo htmlspecialchars($selectedVisit['FullName']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Gender:</label>
                    <input type="text" class="form-control" name="gender" value="<?php echo htmlspecialchars($selectedVisit['Gender']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="test_type">Test Type</label>
                    <select class="form-control" id="test_type" name="test_type" required>
                        <option value="">-- Select Test Type --</option>
                        <option value="Malaria Test">Malaria Test</option>
                        <option value="Typhoid Test (Widal Test)">Typhoid Test (Widal Test)</option>
                        <option value="Blood Glucose Test">Blood Glucose Test</option>
                        <option value="Urinalysis">Urinalysis</option>
                        <option value="Stool Test (Ova & Parasites)">Stool Test (Ova & Parasites)</option>
                        <option value="Full Hemogram (CBC)">Full Hemogram (CBC)</option>
                        <option value="Hepatitis B Test">Hepatitis B Test</option>
                        <option value="HIV Test">HIV Test</option>
                        <option value="X-ray">X-ray</option>
                        <option value="Ultrasound">Ultrasound</option>
                        <option value="Other">Other (Specify in notes)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="clinical_notes">Clinical Notes</label>
                    <textarea class="form-control" id="clinical_notes" name="clinical_notes" rows="4" placeholder="Enter any relevant clinical information..."></textarea>
                </div>

                <div class="form-group">
                    <label for="urgency">Urgency</label>
                    <select class="form-control" id="urgency" name="urgency">
                        <option value="routine">Routine</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Submit Lab Request</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            No visit selected or error retrieving visit details. Please ensure you're accessing this page from a valid appointment.
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>