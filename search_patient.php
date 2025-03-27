<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['patient_name']) && isset($_POST['phone']) && isset($_POST['email'])) {
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    
    $sql = "SELECT * FROM patients WHERE 
            CONCAT(FirstName, ' ', MiddleName, ' ', LastName) = ? 
            AND PhoneNumber = ? 
            AND Email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $patient_name, $phone, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h3>Matching Records:</h3><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['PatientID'] . " - " . $row['FirstName'] . " " . $row['MiddleName'] . " " . $row['LastName'] . 
                 " - " . $row['PhoneNumber'] . " - " . $row['Email'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No matching patient records found.</p>";
    }
    
    $stmt->close();
}

$conn->close();
?>
