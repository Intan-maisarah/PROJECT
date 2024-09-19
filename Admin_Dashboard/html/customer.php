<?php
ob_start();
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the session is started and check if user ID is set
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.<br>";
    exit;
}

// Include the database connection
require '../../connection.php';

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];
$adminEmail = '';
$usertype = '';

// Prepare and execute a query to get the user's email and usertype
$sql = "SELECT email, usertype FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Prepare statement failed: " . $conn->error . "<br>";
    exit;
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    echo "Get result failed: " . $stmt->error . "<br>";
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $adminEmail = $row['email'];
    $usertype = $row['usertype'];
} else {
    echo "User not found.<br>";
    exit;
}

// Close statement and connection
$stmt->close();
$conn->close();


?>


<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="keywords" content="admin, dashboard, printing service" />
  <meta name="description" content="Admin page for staff management" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Customer Management</title>
  <!-- Favicon icon -->
  <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png" />
  <!-- Custom CSS -->
  <link href="../dist/css/style.min.css" rel="stylesheet" />

  <style>
    .preloader {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  flex-direction: column;
}

/* Printer styling */
.printer {
  position: relative;
  width: 120px;
  height: 120px;
}

.printer-top {
  width: 80px;
  height: 20px;
  background: #666;
  border-radius: 10px 10px 0 0;
  position: absolute;
  top: 0;
  left: 20px;
}

.paper-input-slot {
  width: 80px;
  height: 10px;
  background: #444;
  border-radius: 3px;
  position: absolute;
  top: 20px;
  left: 20px;
}

.printer-body {
  width: 120px;
  height: 60px;
  background: #333;
  border-radius: 5px;
  position: absolute;
  top: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.paper {
  width: 80px;
  height: 50px;
  background: #fff;
  border: 2px solid #333;
  border-radius: 3px;
  position: relative;
  animation: paper-print 2s infinite;
}

.printer-tray {
  width: 100px;
  height: 10px;
  background: #333;
  border-radius: 0 0 5px 5px;
  position: absolute;
  bottom: 0;
  left: 10px;
}

/* Animation for the paper printing effect */
@keyframes paper-print {
  0% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(20px);
  }
  100% {
    transform: translateY(0);
  }
}
  </style>
 
</head>

<body>
  <!-- ============================================================== -->
  <!-- Preloader - style you can find in spinners.css -->
  <!-- ============================================================== -->
  <div class="preloader">
  <div class="printer">
    <div class="printer-top"></div>
    <div class="paper-input-slot"></div>
    <div class="printer-body">
      <div class="paper"></div>
    </div>
    <div class="printer-tray"></div>
  </div>
</div>


  
  <!-- ============================================================== -->
  <!-- Main wrapper -->
  <!-- ============================================================== -->
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-header-position="absolute">
    
    <!-- ============================================================== -->
    <!-- Topbar header -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin5">
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin5">
          <a class="navbar-brand" href="admin_page.html">
            <b class="logo-icon">
              <img src="../../assets/images/logo.png" alt="homepage" style="width: 60px; height: auto;" />
            </b>
          </a>
        </div>
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
          <ul class="navbar-nav float-end">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../assets/images/users/1.jpg" alt="user" class="rounded-circle" width="31" />
              </a>
              <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="pages_profile.php"><i class="mdi mdi-account m-r-5 m-l-5"></i> My Profile</a>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    
    <!-- ============================================================== -->
    <!-- Sidebar -->
    <!-- ============================================================== -->
    <?php
    if ($usertype === 'ADMIN') {
        include 'sidebarAdmin.php';
    } else {
        include 'sidebarStaff.php';
    }
    ?>
    
    <!-- ============================================================== -->
    <!-- Page wrapper -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
   <!--php coding for customer-->
   <?php
// Connect to the database
include('../../connection.php'); // Include your database connection file

// Check if action is set in the URL
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

switch($action) {
    case 'view':
        // View customers
        $query = "SELECT * FROM users WHERE userType = 'user'";
        $result = mysqli_query($conn, $query);

        echo "<h2>Customer List</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone Number</th><th>Address</th><th>Actions</th></tr>";
        
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['contact'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td>
                    <a href='customer.php?action=edit&id=" . $row['id'] . "'>Edit</a> | 
                    <a href='customer.php?action=delete&id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        
        echo "</table>";
        break;

    case 'edit':
        // Edit customer
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Update customer data
                $name = $_POST['name'];
                $email = $_POST['email'];
                $contact = $_POST['contact'];
                $address = $_POST['address'];
                $updateQuery = "UPDATE users SET name='$name', email='$email', contact='$contact',address='$address' WHERE id='$id'";
                mysqli_query($conn, $updateQuery);
                header("Location: customer.php?action=view");
            } else {
                // Fetch current customer data for editing
                $query = "SELECT * FROM users WHERE id='$id'";
                $result = mysqli_query($conn, $query);
                $customer = mysqli_fetch_assoc($result);
                ?>
                <h2>Edit Customer</h2>
                <form method="POST" action="">
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo $customer['name']; ?>" required><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $customer['email']; ?>" required><br>
                    <label for="email">Phone Number:</label>
                    <input type="number" name="contact" value="<?php echo $customer['contact']; ?>" required><br>
                    <label for="email">Address:</label>
                    <input type="text" name="address" value="<?php echo $customer['address']; ?>" required><br>
                    <input type="submit" value="Update">
                </form>
                <?php
            }
        }
        break;

    case 'delete':
        // Delete customer
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $deleteQuery = "DELETE FROM users WHERE id='$id'";
            mysqli_query($conn, $deleteQuery);
            header("Location: customer.php?action=view"); // Redirect to view after deleting
        }
        break;

    default:
        // Default action is to view customers
        header("Location: customer.php?action=view");
        break;
}
?>

    <!-- ============================================================== -->
    <!-- Footer -->
    <!-- ============================================================== -->
    <footer class="footer text-center">
      All Rights Reserved by Your Company. Designed and Developed by <a href="https://www.wrappixel.com">WrapPixel</a>.
    </footer>
  </div>
  </div>

  <!-- ============================================================== -->
  <!-- All Jquery -->
  <!-- ============================================================== -->
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap tether Core JavaScript -->
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../dist/js/app-style-switcher.js"></script>
  <script src="../dist/js/waves.js"></script>
  <script src="../dist/js/sidebarmenu.js"></script>
  <script src="../dist/js/custom.js"></script>
</body>

</html>

<?php
ob_end_flush();
?>
