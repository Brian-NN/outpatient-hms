<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Receptionist') {
    header("Location: reception_login.php");
    exit;

}

$doctor_name = $_SESSION['username']; 
?>
<?php 
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: reception_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reception</title>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css.map">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.css.map">
     <link rel="stylesheet" href="bootstrap-grid.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-grid.min.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.css.map">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.min.css">
     <link rel="stylesheet" href="bootstrap/css/bootstrap-reboot.min.css.map">
   
     <link rel="stylesheet" href="css/e-commerce.css">

    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js.map"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js.map"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="bootstrap/js/bootstrap.js.map"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js.map"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: white;
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
        .button-container {
            text-align: center;
            
        }
        .btn-lg {
            width: 250px;
            height: 100px;
            margin: 20px;
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
            <a href="patient_registration.php">New patient</a>
            <a href="patient_visit.php">Visiting Patient</a>
            <a href="registered_patients.php">Records</a>
            <span style="margin-left: 15px; font-weight: bold; color: white;">Miss. <?php echo $doctor_name; ?></span>
              <form method="POST" style="display: inline-block; margin-left: 10px;">
                    <button type="submit" name="logout" style="background: none; border: none; cursor: pointer; color: red; font-size: 18px;">
                        <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
                    </button>
                </form>


        </div>
        
    </div>
    <div class="button-container">
        <a href="patient_registration.php"><button class="btn btn-primary btn-lg">First Time Patient</button></a>
        <a href="patient_visit.php"><button class="btn btn-outline-success btn-lg">Visiting Patient</button></a>
        <a href="appointment.php"><button class="btn btn-danger btn-lg">Appointment</button></a>
    </div>

</body>
</html>