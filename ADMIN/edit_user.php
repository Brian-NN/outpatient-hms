<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    die("Invalid Request");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $user_role = $_POST['user_role'];
    $user_password = $_POST['user_password'];

    
    $updateQuery = "UPDATE users SET name=?, user_role=?, user_password=? WHERE user_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $name, $user_role, $user_password, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully'); window.location='admin_dashboard.php?index';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role:</label>
            <input type="text" name="user_role" class="form-control" value="<?php echo htmlspecialchars($user['user_role']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="text" name="user_password" class="form-control" value="<?php echo htmlspecialchars($user['user_password']); ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update User</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
