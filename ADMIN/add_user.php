<?php 

$servername = "localhost";
$username = "root";
$password = "";
$database = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $user_role = $_POST['user_role'];
    $user_password = $_POST['user_password'];

    
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

    
    $insertQuery = "INSERT INTO users (name, user_role, user_password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $name, $user_role, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('User added successfully'); window.location='admin_dashboard.php?index';</script>";
    } else {
        echo "Error adding user: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Add New User</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role:</label>
            <input type="text" name="user_role" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="user_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
