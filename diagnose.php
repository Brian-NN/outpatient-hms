<?php   

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$testRequestId = filter_input(INPUT_GET, 'request_id', FILTER_VALIDATE_INT);
$visitId = filter_input(INPUT_GET, 'visit_id', FILTER_VALIDATE_INT);

if (!$testRequestId) {
    die("Invalid or missing test request ID.");
}

try {
   
    $sql = "SELECT 
                lr.Test_Result_ID, 
                lr.Results,
                lr.Test_Type,
                lr.Clinical_Notes,
                lr.Conclusion, 
                lr.CreatedAt, 
                lq.VisitId, 
                t.TriageID,  
                CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
                p.Gender, 
                p.PatientID,
                p.DateOfBirth,
                v.CreatedAt
            FROM lab_results lr
            INNER JOIN lab_requests lq ON lr.Test_Request_ID = lq.Test_Request_ID
            INNER JOIN visits v ON lq.VisitId = v.VisitID
            LEFT JOIN triage t ON t.VisitId = v.VisitID  
            INNER JOIN patients p ON v.PatientId = p.PatientID
            WHERE lr.Test_Request_ID = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $testRequestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        
        $debugInfo = [
            'test_request_exists' => checkIfExists($conn, 'lab_requests', 'Test_Request_ID', $testRequestId),
            'lab_result_exists' => checkIfExists($conn, 'lab_results', 'Test_Request_ID', $testRequestId),
            'visit_id_provided' => $visitId
        ];
        
        die("No records found for this Test Request ID. Debug info: " . json_encode($debugInfo));
    }
    
    $patientData = $result->fetch_assoc();
    $visitId = $patientData['VisitId'] ?? $visitId;
    $triageId = $patientData['TriageID'] ?? null;
    
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}


function checkIfExists($conn, $table, $column, $value) {
    $checkSql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $value);
    $checkStmt->execute();
    $result = $checkStmt->get_result()->fetch_assoc();
    return $result['count'] > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Diagnosis & Prescription Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            max-width: 800px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .patient-header {
            background-color: #e9f7fe;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .medicine-entry {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .form-section {
            margin-bottom: 25px;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-primary">Patient Diagnosis & Prescription</h2>
        
        <div class="patient-header">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Patient Name:</strong> <?= htmlspecialchars($patientData['FullName']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($patientData['Gender']) ?></p>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($patientData['DateOfBirth']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Patient ID:</strong> <?= htmlspecialchars($patientData['PatientID']) ?></p>
                    <p><strong>Visit Date:</strong> <?= htmlspecialchars($patientData['CreatedAt']) ?></p>
                    <p><strong>Test Recorded At:</strong> <?= htmlspecialchars($patientData['CreatedAt']) ?></p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="mb-3">Lab Test Results</h4>
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Test Type:</strong> <?= nl2br(htmlspecialchars($patientData['Test_Type'])) ?></p>
                    <p><strong>Clinical Notes:</strong> <?= nl2br(htmlspecialchars($patientData['Clinical_Notes'])) ?></p>
                    <p><strong>Results:</strong> <?= nl2br(htmlspecialchars($patientData['Results'])) ?></p>
                    <p><strong>Conclusion:</strong> <?= nl2br(htmlspecialchars($patientData['Conclusion'])) ?></p>
                </div>
            </div>
        </div>

        <form id="prescription-form" action="process_prescription2.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="visitId" value="<?= htmlspecialchars($visitId) ?>">
            <input type="hidden" name="test_request_id" value="<?= htmlspecialchars($testRequestId) ?>">
            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patientData['PatientID']) ?>">
            <input type="hidden" name="triage_id" value="<?= htmlspecialchars($triageId) ?>">

            <!-- Diagnosis -->
            <div class="form-section">
                <h4 class="mb-3">Diagnosis</h4>
                <div class="mb-3">
                    <label for="diagnosis" class="form-label required-field">Diagnosis</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
                </div>
            </div>

            <!-- Prescription Section -->
            <div class="form-section">
                <h4 class="mb-3">Prescription</h4>
                <div id="medicine-list">
                    <div class="medicine-entry">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label required-field">Medicine Name</label>
                                <input type="text" class="form-control medicine" name="medicine[]" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required-field">Frequency</label>
                                <select class="form-select" name="frequency[]">
                                    <option value="Once a day">Once a day</option>
                                    <option value="Twice a day">Twice a day</option>
                                    <option value="Three times a day">Three times a day</option>
                                    <option value="As needed">As needed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required-field">Quantity</label>
                                <input type="number" class="form-control quantity" name="quantity[]" required>
                                <div class="error-message stock-error"></div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-medicine" onclick="removeMedicine(this)">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-medicine" class="btn btn-outline-primary mt-2" onclick="addMedicine()">
                    Add Another Medicine
                </button>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    Submit Diagnosis & Prescription
                </button>
            </div>
        </form>
    </div>

    <script>
    function addMedicine() {
        const medicineList = document.getElementById('medicine-list');
        const newEntry = document.createElement('div');
        newEntry.className = 'medicine-entry';
        newEntry.innerHTML = `
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label required-field">Medicine Name</label>
                    <input type="text" class="form-control medicine" name="medicine[]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label required-field">Frequency</label>
                    <select class="form-select" name="frequency[]">
                        <option value="Once a day">Once a day</option>
                        <option value="Twice a day">Twice a day</option>
                        <option value="Three times a day">Three times a day</option>
                        <option value="As needed">As needed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label required-field">Quantity</label>
                    <input type="number" class="form-control quantity" name="quantity[]" required>
                    <div class="error-message stock-error"></div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm w-100 remove-medicine" onclick="removeMedicine(this)">Remove</button>
                </div>
            </div>
        `;
        medicineList.appendChild(newEntry);
    }

    function removeMedicine(button) {
        const medicineEntries = document.querySelectorAll('.medicine-entry');
        if (medicineEntries.length > 1) {
            button.closest('.medicine-entry').remove();
        } else {
            alert('At least one medicine entry is required.');
        }
    }

    
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('medicine') || e.target.classList.contains('quantity')) {
            const entry = e.target.closest('.medicine-entry');
            const medicine = entry.querySelector('.medicine').value;
            const quantity = entry.querySelector('.quantity').value;
            const errorElement = entry.querySelector('.stock-error');

            if (medicine && quantity) {
                fetch('check_inventory.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `medicine=${encodeURIComponent(medicine)}&quantity=${encodeURIComponent(quantity)}&csrf_token=<?= $_SESSION['csrf_token'] ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'insufficient') {
                        errorElement.textContent = data.message + ' (Available: ' + data.available + ')';
                    } else {
                        errorElement.textContent = '';
                    }
                })
                .catch(error => {
                    errorElement.textContent = 'Error checking inventory';
                });
            } else {
                errorElement.textContent = '';
            }
        }
    });

    ation
    document.getElementById('prescription-form').addEventListener('submit', function(e) {
        let hasErrors = false;
        
        
        document.querySelectorAll('.stock-error').forEach(errorElement => {
            if (errorElement.textContent.includes('Not enough')) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Cannot submit prescription. Some medications have insufficient stock.');
            return false;
        }

        return true;
    });
    </script>
</body>
</html>