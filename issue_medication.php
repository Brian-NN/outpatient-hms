<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['DiagnosisID']) && is_numeric($_GET['DiagnosisID'])) {
    $diagnosisID = intval($_GET['DiagnosisID']);

     diagnoses table
    $updateSQL = "UPDATE diagnoses SET Status = 'Issued' WHERE DiagnosisID = ?";
    $updateStmt = $conn->prepare($updateSQL);
    $updateStmt->bind_param("i", $diagnosisID);
    $updateStmt->execute();
    $updateStmt->close();

    
    $sql = "SELECT d.DiagnosisID, d.VisitID, d.Diagnosis, d.Treatment, 
                   p.FirstName, p.MiddleName, p.LastName 
            FROM diagnoses d
            JOIN visits v ON d.VisitID = v.VisitID
            JOIN patients p ON v.PatientID = p.PatientID
            WHERE d.DiagnosisID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $diagnosisID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: pharmacy.php?error=No record found");
        exit();
    }
    $stmt->close();
} else {
    header("Location: pharmacy.php?error=Invalid request");
    exit();
}


$searchQuery = "";
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doSearch'])) {
    $searchQuery = trim($_POST['search']);
    if (!empty($searchQuery)) {
        $sqlInv = "SELECT ItemName FROM pharmacy_inventory WHERE ItemName LIKE ?";
        $stmtInv = $conn->prepare($sqlInv);
        $like = "%" . $searchQuery . "%";
        $stmtInv->bind_param("s", $like);
        $stmtInv->execute();
        $resultInv = $stmtInv->get_result();
        while($rowInv = $resultInv->fetch_assoc()){
            $searchResults[] = $rowInv['ItemName'];
        }
        $stmtInv->close();
    }
}


$selectedItem = "";
if(isset($_GET['selectedItem'])) {
    $selectedItem = $_GET['selectedItem'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Issue Medication</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          background-color: #f4f4f4;
          margin: 0;
          padding: 20px;
          text-align: center;
      }
      .container {
          max-width: 500px;
          margin: auto;
          padding: 20px;
          background: white;
          border-radius: 8px;
          box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
          text-align: left;
      }
      .details {
          margin-bottom: 20px;
          padding: 10px;
          background-color: #eef;
          border-radius: 5px;
      }
      .search-container {
          margin-bottom: 20px;
      }
      .search-results {
          margin-top: 10px;
      }
      .search-results a {
          display: block;
          padding: 5px;
          text-decoration: none;
          background: #f0f0f0;
          margin: 2px 0;
          border-radius: 3px;
          color: #333;
      }
      h2 {
          color: #21618c;
      }
      label {
          font-weight: bold;
          display: block;
          margin: 10px 0 5px;
      }
      input[type="text"] {
          width: 100%;
          padding: 8px;
          margin-bottom: 10px;
          border: 1px solid #ccc;
          border-radius: 4px;
      }
      button {
          background-color: #28a745;
          color: white;
          border: none;
          padding: 10px;
          cursor: pointer;
          width: 100%;
          border-radius: 4px;
      }
      button:hover {
          background-color: #218838;
      }
  </style>
</head>
<body>

<div class="container">
    <h2>Issue Medication</h2>
    <!-- Display patient details above the form -->
    <div class="details">
        <p><strong>Patient Name:</strong> <?= htmlspecialchars($row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']) ?></p>
        <p><strong>Diagnosis:</strong> <?= htmlspecialchars($row['Diagnosis']) ?></p>
        <p><strong>Treatment:</strong> <?= htmlspecialchars($row['Treatment']) ?></p>
    </div>
    
    <!-- Search form for Item Name -->
    <div class="search-container">
        <!-- The action includes the DiagnosisID so the GET parameter is maintained -->
        <form method="POST" action="issue_medication.php?DiagnosisID=<?= $diagnosisID ?>">
            <input type="text" name="search" placeholder="Search by Item Name" value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit" name="doSearch">Search</button>
        </form>
        
        <?php if (!empty($searchResults)): ?>
            <div class="search-results">
                <?php foreach($searchResults as $item): ?>
                    <a href="issue_medication.php?DiagnosisID=<?= $diagnosisID ?>&selectedItem=<?= urlencode($item) ?>">
                        <?= htmlspecialchars($item) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Main Medication Issue Form -->
    <form method="POST" action="process_issue_medication.php">
        <label>Diagnosis ID</label>
        <input type="text" name="DiagnosisID" value="<?= htmlspecialchars($row['DiagnosisID']) ?>" readonly>

        <label>Visit ID</label>
        <input type="text" name="VisitID" value="<?= htmlspecialchars($row['VisitID']) ?>" readonly>

        <label>Item Name</label>
        <input type="text" name="ItemName" value="<?= htmlspecialchars($selectedItem) ?>" required>

        <label>Quantity</label>
        <input type="number" name="quantity" min="1" step="1" required> <br> <br>
        <button type="submit">Issue Medication</button>
    </form>
</div>

</body>
</html>
