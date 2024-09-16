<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your database connection file
include '../../connection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch the admin's profile data (assuming you have an admin_id session or get the admin's id)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Query to get the admin's profile data
$query = "SELECT name, username, email, contact, address FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind admin ID dynamically
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $name = $row['name'] ?? "N/A";
  $username = $row['username'] ?? "N/A";
  $email = $row['email'] ?? "N/A";
  $contact = $row['contact'] ?? "N/A";
  $address = $row["address"] ?? "N/A";
} else {
    $name = "N/A";
    $username = "N/A";
    $email = "N/A";
    $contact = "N/A";
    $address = "N/A";
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Validate input
    if (empty($name) || empty($email) || empty($contact) || empty($address)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Prepare SQL statement
        include '../../connection.php'; // Reconnect to the database
        $sql = "UPDATE users SET name=?, email=?, contact=?, address=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $contact, $address, $user_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Edit Profile</title>
  <link rel="canonical" href="https://www.wrappixel.com/templates/xtreme-admin-lite/" />
  <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png" />

  <!-- Local CSS -->
  <link href="../dist/css/style.css" rel="stylesheet" />

  <!-- CDN Links for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.6.1/css/flag-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/themify-icons/0.1.2/css/themify-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/crypto-icons/1.1.0/cryptocoins.min.css">

  <!-- Embedded CSS for customization -->
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    
    .topbar {
      background-color: #343a40;
      padding: 10px 0;
    }
    
    .navbar-brand img {
      height: 40px;
    }
    
    .navbar-nav .nav-link {
      color: #ffffff;
    }
    
    .navbar-nav .nav-link:hover {
      color: #cccccc;
    }
    
    .profile-sidebar {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .profile-userpic img {
      border-radius: 50%;
      width: 150px;
      height: 150px;
      margin-bottom: 20px;
    }
    
    .profile-usertitle {
      text-align: center;
    }
    
    .profile-usertitle-name {
      font-size: 20px;
      font-weight: bold;
    }
    
    .profile-usertitle-job {
      font-size: 16px;
      color: #777777;
    }
    
    .profile-userbuttons {
      text-align: center;
      margin-top: 20px;
    }
    
    .profile-userbuttons .btn-primary {
      background-color: #007bff;
      border: none;
    }
    
    .profile-userbuttons .btn-primary:hover {
      background-color: #0056b3;
    }
    
    .profile-usermenu ul {
      list-style: none;
      padding: 0;
    }
    
    .profile-usermenu ul li {
      margin-bottom: 10px;
    }
    
    .profile-usermenu ul li a {
      text-decoration: none;
      color: #007bff;
    }
    
    .profile-usermenu ul li a:hover {
      text-decoration: underline;
    }
    
    .footer {
      background-color: #343a40;
      color: #ffffff;
      padding: 20px;
      text-align: center;
      position: relative;
      bottom: 0;
      width: 100%;
    }

    /* Edit Profile Button Styles */
    .btn-primary {
      background-color: #007bff;
      border: none;
      border-radius: 4px;
      width: 180px;
      color: #ffffff;
      font-size: 16px;
      font-weight: bold;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      text-decoration: none;
    }

    .btn-primary:active {
      background-color: #004494;
      transform: translateY(1px);
    }

    .btn-primary:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(38, 143, 255, 0.5);
    }
  </style>
  
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <!-- ============================================================== -->
  <!-- Preloader - style you can find in spinners.css -->
  <!-- ============================================================== -->
  <div class="preloader">
    <div class="lds-ripple">
      <div class="lds-pos"></div>
      <div class="lds-pos"></div>
    </div>
  </div>
  <!-- ============================================================== -->
  <!-- Main wrapper - style you can find in pages.scss -->
  <!-- ============================================================== -->
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin5">
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin5">
          <!-- ============================================================== -->
          <!-- Logo -->
          <!-- ============================================================== -->
          <a class="navbar-brand" href="admin_page.php">
            <!-- Logo icon -->
            <b class="logo-icon">
              <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
              <!-- Dark Logo icon -->
              <img src="../../assets/images/logo.png" alt="homepage" style="width: 62px !important; height: auto !important;"/>
            </b>
            <!--End Logo icon -->
            <!-- Logo text -->
          </a>
          <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
              class="ti-menu ti-close"></i></a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
          <!-- ============================================================== -->
          <!-- toggle and nav items -->
          <!-- ============================================================== -->
          <ul class="navbar-nav float-start me-auto">
            <!-- ============================================================== -->
            <!-- Search -->
            <!-- ============================================================== -->
            <li class="nav-item search-box">
              <a class="nav-link waves-effect waves-dark" href="javascript:void(0)"><i
                  class="mdi mdi-magnify fs-4"></i></a>
              <form class="app-search position-absolute">
                <input type="text" class="form-control" placeholder="Search &amp; enter" />
                <a class="srh-btn"><i class="mdi mdi-close"></i></a>
              </form>
            </li>
          </ul>
          <!-- ============================================================== -->
          <!-- Right side toggle and nav items -->
          <!-- ============================================================== -->
          <ul class="navbar-nav float-end">
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <li class="nav-item dropdown">
              <a class="
                    nav-link
                    dropdown-toggle
                    text-muted
                    waves-effect waves-dark
                    pro-pic
                  " href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../assets/images/users/1.jpg" alt="user" class="rounded-circle" width="31" />
              </a>
              <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-account m-r-5 m-l-5"></i> My
                  Profile</a>
                <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-wallet m-r-5 m-l-5"></i> My
                  Balance</a>
                <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-email m-r-5 m-l-5"></i> Inbox</a>
              </ul>
            </li>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
          </ul>
        </div>
      </nav>
    </header>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->

    <aside class="left-sidebar" data-sidebarbg="skin6">
      <!-- Sidebar scroll-->
      <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
          <ul id="sidebarnav">
            <!-- User Profile-->
            <li>
              <!-- User Profile-->
              <div class="user-profile d-flex no-block dropdown m-t-20">
                <div class="user-pic">
                  <img src="../assets/images/users/1.jpg" alt="users" class="rounded-circle" width="40" />
                </div>
                <div class="user-content hide-menu m-l-10">
                  <a href="#" class="" id="Userdd" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <h5 class="m-b-0 user-name font-medium">
                      ADMIN <i class="mdi mdi-chevron-down fs-4"></i>
                    </h5>
                    <span class="op-5 user-email"><?php echo htmlspecialchars($email); ?></span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="Userdd">
                    <a class="dropdown-item" href="update_AdminProfile.php"><i class="mdi mdi-account m-r-5 m-l-5"></i> My
                      Profile</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-wallet m-r-5 m-l-5"></i> My
                      Balance</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-email m-r-5 m-l-5"></i>
                      Inbox</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-settings m-r-5 m-l-5"></i>
                      Account
                      Setting</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../../logout.php"><i class="mdi mdi-power m-r-5 m-l-5"></i>
                      Logout</a>
                  </div>
                </div>
              </div>
              <!-- End User Profile-->
            </li>
            <li class="p-15 m-t-10">
              <a href="javascript:void(0)" class="
                    btn
                    d-block
                    w-100
                    create-btn
                    text-white
                    no-block
                    d-flex
                    align-items-center
                  "><i class="mdi mdi-plus-box"></i>
                <span class="hide-menu m-l-5">Create New</span>
              </a>
            </li>
            <!-- User Profile-->
            <li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="admin_page.php" aria-expanded="false"><i
                  class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="pages_profile.php"
                aria-expanded="false"><i class="mdi mdi-account-network"></i><span class="hide-menu">Profile</span></a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="staff.php"
                aria-expanded="false"><i class="mdi mdi-account-network"></i><span class="hide-menu">Staff</span></a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="customer.php"
                aria-expanded="false"><i class="mdi mdi-account-network"></i><span class="hide-menu">Customer</span></a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="table_basic.html"
                aria-expanded="false"><i class="mdi mdi-border-all"></i><span class="hide-menu">Table</span></a>
            </li>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="profile-sidebar">
              
              <div class="profile-usertitle">
                <div class="profile-usertitle-name">
                  <?php echo htmlspecialchars($name); ?>
                </div>
                <div class="profile-usertitle-job">
                  <?php echo htmlspecialchars($username); ?>
                </div>
              </div>
              <div class="profile-userbuttons">
                <a href="change_passwordAdmin.php" class="btn btn-primary">Change Password</a>
              </div>
              <div class="profile-usermenu">
                <ul>
                  <!--<li>
                    <a href="profile.html">
                      <i class="mdi mdi-account"></i> Profile
                    </a>
                  </li>
                  <li>
                    <a href="settings.html">
                      <i class="mdi mdi-settings"></i> Settings
                    </a>
                  </li>-->
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="profile-content">
              <h3>Edit Profile</h3>
              <?php
              if (isset($success)) {
                  echo "<div class='alert alert-success'>$success</div>";
              }
              if (isset($error)) {
                  echo "<div class='alert alert-danger'>$error</div>";
              }
              ?>
              <form action="" method="POST">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" />
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly/>
                </div>
                <div class="form-group">
                  <label for="contact">Contact</label>
                  <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" />
                </div>
                <div class="form-group">
                  <label for="address">Address</label>
                  <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" />
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer">
        © 2024 Xtreme Admin by Wrappixel
      </footer>
    </div>
  </div>

  <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Local JS -->
  <script src="../dist/js/app-style-switcher.js"></script>
  <script src="../dist/js/custom.js"></script>
  <script src="../dist/js/sidebarmen.js"></script>
</body>

</html>
