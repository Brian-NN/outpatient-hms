<?php  
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jamii-hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$admin_name = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}

/* Top Bar */
.top-bar {
    background-color: #21618c;
    color: white;
    width: 100%;
    height: 60px;
    position: fixed;
    border-bottom: 2px solid #154360;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    z-index: 1000;
    top: 0;
    left: 0;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.top-bar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease-in-out;
}

.top-bar a:hover {
    color: #f1c40f;
    text-decoration: underline;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 60px; 
    left: 0;
    height: calc(100vh - 60px);
    width: 250px;
    background-color: #21618c;
    border-right: 2px solid #154360;
    padding: 20px;
    overflow-y: auto;
    transition: all 0.3s ease-in-out;
}

.sidebar a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 5px;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

.sidebar a:hover {
    background-color: #f1c40f;
    color: #21618c;
    transform: scale(1.05);
    font-weight: bold;
}

/* Main Content */
.main-content {
    margin-left: 250px;
    padding: 80px 20px;
    min-height: calc(100vh - 80px);
}

/* Header */
.header {
    background-color: #21618c;
    color: white;
    padding: 15px;
    position: fixed;
    top: 0;
    left: 250px;
    right: 0;
    z-index: 1000;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}

/* Footer */
.footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px;
    position: fixed;
    bottom: 0;
    left: 250px;
    right: 0;
    font-size: 14px;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .sidebar {
        width: 220px;
    }

    .main-content {
        margin-left: 220px;
    }

    .footer {
        left: 220px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }

    .footer {
        left: 0;
    }
}

    </style>
</head>
<body>
       <div class="top-bar">
        <div>
            <span class="fw-bold"> JAMII COMMUNITY HOSPITAL</span> 
        </div>
        <div>
            <span style="margin-left: 15px; font-weight: bold;"><?php echo $admin_name; ?></span>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" style="background: red; border: none; cursor: pointer; color: white;">
                          <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="container mt-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class=" sidebar">
                    <a href="admin_dashboard.php?index" class="list-group-item">Dashboard</a>
                    <a href="admin_dashboard.php?patients" class="list-group-item">Patients</a>
                    <a href="admin_dashboard.php?visits" class="list-group-item">Visits</a>
                    <a href="admin_dashboard.php?appointments" class="list-group-item">Appointments</a>
                    <a href="admin_dashboard.php?tests" class="list-group-item">Lab Tests</a>
                     <a href="admin_dashboard.php?pharmacy" class="list-group-item">Pharmacy Inventory</a>
                    <a href="admin_dashboard.php?hr" class="list-group-item">Human resource</a>
                </div>       
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <?php
                if (isset($_GET['index'])) {
                    include('index.php');
                } elseif (isset($_GET['patients'])) {
                    include('patients.php');
                }elseif (isset($_GET['visits'])) {
                    include('visits.php');
                } elseif (isset($_GET['tests'])) {
                    include('lab_tests.php'); 
                } elseif (isset($_GET['pharmacy'])) {
                    include('pharmacy.php'); 
                } elseif (isset($_GET['hr'])) {
                    include('hr.php'); 
                 }elseif (isset($_GET['appointments'])) {
                    include('appointments.php'); 
                 }else {
                    include('index.php');
                }
                ?>
            </div>
        </div>
    </div>
</body>
    <footer class="footer">
        <p class="mb-0">&copy; 2024 Outpatient Hospital Management System</p>
    </footer>

</html>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>