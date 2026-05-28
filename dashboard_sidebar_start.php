<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>EUSEBIA PAZ ARROYO NATIONAL HIGH SCHOOL</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* Sidebar - Resident Color Theme (#0b2b5c / #0f3b7a) */
        #accordionSidebar {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%) !important;
        }
        #accordionSidebar .sidebar-brand {
            color: #fff !important;
        }
        #accordionSidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        #accordionSidebar .nav-item .nav-link:hover,
        #accordionSidebar .nav-item.active .nav-link {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.12) !important;
        }
        #accordionSidebar .sidebar-heading {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        #accordionSidebar hr.sidebar-divider {
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        #sidebarToggle {
            background: rgba(255, 255, 255, 0.2) !important;
        }
        #sidebarToggle:hover {
            background: rgba(255, 255, 255, 0.35) !important;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%)!important;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admn_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    
                </div>
                <div class="sidebar-brand-text">Administrator Dashboard </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="admn_dashboard.php">
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Grade Level Enrollment
            </div>


            <li class="nav-item">
                <a class="nav-link" href="admn_seven.php">
                    <span>GRADE-7</span></a>
            </li>

            <!-- Certificate of Residency -->
            <li class="nav-item">
                <a class="nav-link" href="admn_eight.php">
                    <span>GRADE-8</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admn_nine.php">
                    <span>GRADE-9 </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admn_ten.php">
                    <span>GRADE-10</span></a>
            </li>



            <!-- Barangay Clearance -->
            <li class="nav-item">
                <a class="nav-link" href="admn_eleven.php">
                    <span>GRADE-11</span></a>
            </li>

            <!-- Certificate of Indigency -->
            <li class="nav-item">
                <a class="nav-link" href="admn_twelve.php">
                    <span>GRADE-12</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a class="nav-link" href="admn_classlist.php">
                    <span>Class List</span></a>
            </li>
<li class="nav-item">
                <a class="nav-link" href="admn_archive.php">
                    <span>Archive</span></a>
            </li>
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="index.php" id="userDropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-800 small"><?= $userdetails['surname']?>, <?= $userdetails['firstname']?> <?= $userdetails['mname']?></span>
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                </a>
                            </li>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                
                <!-- End of Topbar -->