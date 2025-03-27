
<style>
   
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
        font-size: 50px;
        margin-bottom: 15px;
    }

    
    .patients { background-color: #007bff; color: white; }
    .visits { background-color: #28a745; color: white; }
    .lab_tests { background-color: #ff5733; color: white; }
    .pharmacy { background-color: #6f42c1; color: white; }
    .hr { background-color: #fd7e14; color: white; }
    .users { background-color: #343a40; color: white; }

    
    @media (max-width: 768px) {
        .card {
            margin-bottom: 15px;
        }
    }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
</style>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<br><br>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "jamii-hms";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function getCount($conn, $table) {
    $query = "SELECT COUNT(*) AS total FROM $table";
    $result = $conn->query($query);
    $count = 0;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['total'];
    }
    return $count;
}


$patient_count = getCount($conn, "patients");
$visit_count = getCount($conn, "visits");
$lab_results_count = getCount($conn, "lab_results");
$pharmacy_inventory_count = getCount($conn, "pharmacy_inventory");
$hr_count = getCount($conn, "human_resource");
$user_count = getCount($conn, "users");

;
?>


<div class="row">
    <div class="col-md-4">
        <div class="card patients" onclick="location.href='patients.php';">
            <i class="fas fa-user-injured"></i>
            <h5 class="card-title">Patients</h5>
            <p class="small-text">Total: <strong><?php echo $patient_count; ?></strong></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card visits" onclick="location.href='visits.php';">
            <i class="fas fa-calendar-check"></i>
            <h5 class="card-title">Visits</h5>
            <p class="small-text">Total: <strong><?php echo $visit_count; ?></strong></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card lab_tests" onclick="location.href='lab_results.php';">
            <i class="fas fa-vial"></i>
            <h5 class="card-title">Lab Results</h5>
            <p class="small-text">Total: <strong><?php echo $lab_results_count; ?></strong></p>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col-md-4">
        <div class="card pharmacy" onclick="location.href='pharmacy.php';">
            <i class="fas fa-pills"></i>
            <h5 class="card-title">Pharmacy Inventory</h5>
            <p class="small-text">Total: <strong><?php echo $pharmacy_inventory_count; ?></strong></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card hr" onclick="location.href='hr.php';">
            <i class="fas fa-users"></i>
            <h5 class="card-title">HR</h5>
            <p class="small-text">Total: <strong><?php echo $hr_count; ?></strong></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card users" onclick="location.href='users.php';">
            <i class="fas fa-user"></i>
            <h5 class="card-title">Users</h5>
            <p class="small-text">Total: <strong><?php echo $user_count; ?></strong></p>
        </div>
    </div>
</div> <br>

<?php
	$query = "SELECT user_id, name, user_role, user_password FROM users";
$result = $conn->query($query);
?>
<div class="container">
    <h2 align="center">Users' List</h2>
    <a href="add_user.php"><button class="btn btn-outline-success">Add User</button></a> 
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                 <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['user_role']}</td>
                        <td>
                            <a href='edit_user.php?id={$row['user_id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='delete_user.php?id={$row['user_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No users found</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>


