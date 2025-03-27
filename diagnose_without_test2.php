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


$visitId = isset($_POST['visitId']) ? $_POST['visitId'] : '';
if (empty($visitId) && isset($_GET['visitId'])) {
    $visitId = $_GET['visitId']; 
}
if (empty($visitId) && isset($_SESSION['visitId'])) {
    $visitId = $_SESSION['visitId']; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Diagnosis & Prescription Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            margin-top: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .remove-medicine {
            background: red;
            color: white;
            padding: 5px;
            margin-top: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .remove-medicine:hover {
            background: darkred;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Patient Diagnosis & Prescription Form</h2>

    <form id="prescription-form" action="process_prescription.php" method="POST">
        <input type="hidden" name="visitId" value="<?= htmlspecialchars($visitId); ?>">

        <!-- Diagnosis -->
        <label for="diagnosis">Diagnosis:</label>
        <textarea id="diagnosis" name="diagnosis" rows="3" required></textarea>

        <!-- Prescription Section -->
        <h3>Prescription</h3>
        <div id="medicine-list">
            <div class="medicine-entry">
                <label for="medicine">Medicine Name:</label>
                <input type="text" name="medicine[]" class="medicine" required>


                <label for="frequency">Frequency:</label>
                <select name="frequency[]">
                    <option value="Once a day">Once a day</option>
                    <option value="Twice a day">Twice a day</option>
                    <option value="Three times a day">Three times a day</option>
                    <option value="As needed">As needed</option>
                </select>

                <label for="quantity">Quantity (Total Tablets/Bottles):</label>
                <input type="number" name="quantity[]" class="quantity" required>
                <span class="error"></span>

                <!-- Remove Button -->
                <button type="button" class="remove-medicine" onclick="removeMedicine(this)">Remove</button>
            </div>
        </div>

        <!-- Add More Medicine Button -->
        <button type="button" onclick="addMedicine()">Add Another Medicine</button>

        <!-- Submit Button -->
        <button type="submit">Submit Diagnosis & Prescription</button>
    </form>
</div>

<script>
    function addMedicine() {
        let medicineList = document.getElementById("medicine-list");
        let newMedicine = document.createElement("div");
        newMedicine.classList.add("medicine-entry");

        newMedicine.innerHTML = `
            <label for="medicine">Medicine Name:</label>
            <input type="text" name="medicine[]" class="medicine" required>

       
            <label for="frequency">Frequency:</label>
            <select name="frequency[]">
                <option value="Once a day">Once a day</option>
                <option value="Twice a day">Twice a day</option>
                <option value="Three times a day">Three times a day</option>
                <option value="As needed">As needed</option>
            </select>

            <label for="quantity">Quantity (Total Tablets/Bottles):</label>
            <input type="number" name="quantity[]" class="quantity" required>
            <span class="error"></span>

            <!-- Remove Button -->
            <button type="button" class="remove-medicine" onclick="removeMedicine(this)">Remove</button>
        `;
        
        medicineList.appendChild(newMedicine);
    }

    function removeMedicine(button) {
        let medicineEntry = button.parentElement;
        medicineEntry.remove();
    }

    
$(document).on("input", ".medicine, .quantity", function () {
    let medicine = $(this).closest(".medicine-entry").find(".medicine").val();
    let quantity = $(this).closest(".medicine-entry").find(".quantity").val();
    let errorElement = $(this).closest(".medicine-entry").find(".error");

    console.log("Sending:", medicine, quantity); 

    if (medicine !== "" && quantity !== "") {
        $.ajax({
            url: "check_inventory.php",
            method: "POST",
            data: { medicine: medicine, quantity: quantity },
            dataType: "json", 
            success: function (response) {
                console.log("Response:", response); 
                if (response.status === "insufficient") {
                    errorElement.text(response.message + " (Available: " + response.available + ")");
                } else {
                    errorElement.text(""); 
                }
            },
            error: function () {
                console.log("Error fetching stock data");
            }
        });
    }
});


    
  $("#prescription-form").submit(function (event) {
    let hasError = false;

    $(".error").each(function () {
        if ($(this).text().includes("Not enough stock")) {
            hasError = true;
        }
    });

    if (hasError) {
        alert("Cannot submit! Some medicines are out of stock.");
        event.preventDefault();
    }
});

</script>

</body>
</html>
