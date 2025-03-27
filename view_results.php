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


if (isset($_GET['request_id']) && is_numeric($_GET['request_id'])) {
    $testRequestId = $_GET['request_id'];

  
    $sql = "SELECT 
                lr.Test_Result_ID, 
                lr.Test_Request_ID,
                lr.Test_Type,
                lr.Clinical_Notes,
                lr.Results, 
                lr.Conclusion, 
                lr.CreatedAt, 
                CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
                p.Gender
                
            FROM lab_results lr
            INNER JOIN lab_requests lq ON lr.Test_Request_ID = lq.Test_Request_ID
            INNER JOIN visits v ON lq.VisitId = v.VisitID
            INNER JOIN patients p ON v.PatientId = p.PatientID
            WHERE lr.Test_Request_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $testRequestId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Invalid or missing request ID.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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
    
        .container {
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #21618c;
        }
        .details {
            margin-top: 20px;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 8px 12px;
            background-color: #21618c;
            color: white;
            border-radius: 5px;
            text-decoration: none;
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

<div class="container">
    <h2>Lab Test Results</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="details">
                <p><strong>Patient Name:</strong> <?php echo htmlspecialchars($row['FullName']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['Gender']); ?></p>
                <p><strong>Test Recorded At:</strong> <?php echo htmlspecialchars($row['CreatedAt']); ?></p>
                      <p><strong>Test Type:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($row['Test_Type'])); ?></p>
                <p><strong>Clinical Notes:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($row['Clinical_Notes'])); ?></p>
                <p><strong>Results:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($row['Results'])); ?></p>
                <p><strong>Conclusion:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($row['Conclusion'])); ?></p>


            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No results found for this test request.</p>
    <?php endif; ?>

<a href="diagnose.php?request_id=<?php echo $testRequestId; ?>" class="btn btn-primary">Diagnose Patient</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
