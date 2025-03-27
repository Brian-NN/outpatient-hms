<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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
        .top-bar {
            background-color: #21618c;
            color: white;
            width: 100%;
            height: 60px;
            position: fixed;
            border-bottom:2px solid #154360;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            z-index: 1000;
            top: 0;
            left: 0;
        }
        .top-bar a {
            color: white;
            text-decoration: none;

        }
        .top-bar a:hover {
            text-decoration: underline;
        }
        .sidebar {
            background-color: #21618c;
            color: white;
            height: 100vh;
            width: 220px;
            position: fixed;
            padding-top: 70px;
            border-right: 2px solid #154360;
            top: 0;
            left: 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            top: 0;
            left: 0;
        }
        .sidebar a:hover {
            background-color: #7fb3d5 ;
            color : #fff;
        }
        .content {
            margin-left:230px ;
            margin-top:70px ;
            padding:20px ;
        }
        .small-text {
            font-size: 14px;
            opacity: 0.8;
        }
        .card {
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card i {
            font-size: 40px;
            margin-bottom: 10px;

        }
        .reception { background-color: #007bff; color: white;}
        .triage {background-color: #28a745; color: white;}
        .doctors { background-color: #ffc107; color: white; } 
        .lab { background-color: #dc3545; color: white; } 
        .pharmacy { background-color: #4a235a; color: white; }  
        .billing { background-color: #0b5345 ; color: white; } 
        .our-user {
            background-color: orange;
            color: white;
        }
        .ward{ background-color:pink; }
    </style>

</head>
<body>
    <!-- Code for topbar -->
    <div class="top-bar">
        <div>
            <span class="fw-bold"> JAMII COMMUNITY HOSPITAL</span> 
        </div>
        <div>
            
        </div>
    </div>
    <nav class="sidebar">
        <h4>Dashboard</h4>
        <a href="reception.php">Reception</a>
        <a href="triage.php">Triage</a>
        <a href="doctors.php">Doctor</a>
        <a href="lab.php">Lab</a>
        <a href="pharmacy.php">Pharmacy</a>
        <a href="billing.php">Billing</a>
        
    </nav>
    <div class="content">
        <h4 class="mb-4" align="center">Select your department</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="card reception" onclick="location.href='reception.php';">
                    <i class="fas fa-user-circle"></i>
                    <h5 class="card-tittle">Reception</h5>
                    <p class="small-text"> Click to log in to triage</p>
                </div>
                
            </div>
            <div class="col-md-4">
                <div class="card triage" onclick="location.href='triage.php';">
                    <i class="fas fa-heartbeat"></i>
                    <h5 class="card-tittle">Triage</h5>
                    <p class="small-text">Click to log in to triage</p>
                    
                </div>
            </div>
                
            <div class="col-md-4">
                <div class="card doctors" onclick="location.href='doctors.php';">
                    <i class="fas fa-user-md"></i>
                    <h5 class="card-title">Doctors</h5>
                    <p class="small-text">Click to log in to Doctors</p>
                </div>
            </div>
        </div> <br>

            
            
        
        <div class="row">
             <div class="col-md-4">
                <div class="card lab" onclick="location.href='lab.php';">
                    <i class="fas fa-vial"></i>
                    <h5 class="card-title">Lab</h5>
                    <p class="small-text">Click to log in to Lab</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card pharmacy " onclick="location.href='pharmacy.php';">
                    <i class="fas fa-pills text-info"></i>
                    <h5 class="card-title">PHARMACY</h5>
                    <p class="small-text">Click to log in to Pharmacy</p>
                </div>
            </div>
                    
            <div class="col-md-4">
                <div class="card billing " onclick="location.href='billing.php';">
                    <i class="fas fa-file-invoice-dollar text-secondary"></i>
                    <h5 class="card-title">BILLING</h5>
                    <p class="small-text">Click to log in to Billing</p>
                </div>
            </div> 
        </div>
        
      

      
        
    </div>



</body>
</html>