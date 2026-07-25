<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- PWA -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0b2b5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EPAMNHS">
    <link rel="apple-touch-icon" href="icons/pwa/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="icons/pwa/icon-192x192.png">

    <title>EUSEBIA PAZ ARROYO NATIONAL HIGH SCHOOL</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* Sidebar - Student Color Theme (#0b2b5c / #0f3b7a) */
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

        /* ===== Row "Actions" dropdown — Facebook-menu style ===== */
        .actions-dropdown-toggle {
            border-radius: 8px !important;
            font-weight: 600;
            padding: .4rem .9rem !important;
        }
        .actions-dropdown-menu {
            min-width: 240px;
            padding: .5rem;
            border: none;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(11, 43, 92, 0.18), 0 2px 10px rgba(0,0,0,0.10);
        }
        .actions-dropdown-menu .actions-dropdown-header {
            font-weight: 700;
            font-size: .72rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #90959c;
            padding: .4rem .6rem .35rem;
        }
        .actions-dropdown-menu .actions-dropdown-body {
            padding: 0;
        }
        .actions-dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .5rem .55rem;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            color: #050505;
            transition: background .12s ease;
        }
        .actions-dropdown-menu .dropdown-item .action-icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 50%;
            background: #f0f2f5;
            color: #444950;
            font-size: 1rem;
        }
        .actions-dropdown-menu .dropdown-item.item-view .action-icon-badge   { background: #e7f3ff; color: #1877f2; }
        .actions-dropdown-menu .dropdown-item.item-archive .action-icon-badge { background: #f0f2f5; color: #65676b; }
        .actions-dropdown-menu .dropdown-item.item-approve .action-icon-badge { background: #e3f6e8; color: #1a9c4b; }
        .actions-dropdown-menu .dropdown-item.item-reject .action-icon-badge  { background: #fde8e8; color: #e0245e; }
        .actions-dropdown-menu .dropdown-item:hover,
        .actions-dropdown-menu .dropdown-item:focus {
            background: #f2f2f2;
            color: #050505;
            text-decoration: none;
        }
        .actions-dropdown-menu .dropdown-item.text-danger:hover { background: #fdecec; }
        .actions-dropdown-menu form { margin: 0; }
        .actions-dropdown-menu .dropdown-divider {
            margin: .35rem .5rem;
            border-color: #edf0f5;
        }
        .actions-dropdown-menu.dropdown-menu-floating {
            position: fixed !important;
            margin: 0 !important;
            transform: none !important;
            z-index: 3050 !important;
            max-height: 80vh;
            overflow-y: auto;
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
                                        <li class="nav-item">
                <a class="nav-link" href="admn_students.php">
                    <span>Accounts</span></a>
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

            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Administration
            </div>

            <li class="nav-item">
                <a class="nav-link" href="admn_staff_crud.php">
                    <span>Teachers &amp; Advisers</span></a>
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

                        <?php
                            $pendingData = (isset($eusebia) && method_exists($eusebia, 'get_pending_enrollees'))
                                ? $eusebia->get_pending_enrollees()
                                : ['total' => 0, 'items' => []];
                        ?>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - Pending Enrollment Notification -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="pendingApprovalsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <?php if (!empty($pendingData['total'])): ?>
                                <span class="badge badge-danger badge-counter"><?= $pendingData['total'] > 99 ? '99+' : (int) $pendingData['total'] ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - Pending Enrollment -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="pendingApprovalsDropdown" style="max-height:26rem; overflow-y:auto;">
                                <h6 class="dropdown-header">
                                    <?= (int) $pendingData['total'] ?> Pending Enrollment<?= $pendingData['total'] == 1 ? '' : 's' ?>
                                </h6>
                                <?php if (empty($pendingData['items'])): ?>
                                <div class="dropdown-item text-center text-gray-500 py-3">
                                    <i class="fas fa-check-circle text-success mr-1"></i> All caught up, no pending enrollees.
                                </div>
                                <?php else: ?>
                                    <?php foreach ($pendingData['items'] as $item): ?>
                                    <a class="dropdown-item d-flex align-items-center" href="<?= htmlspecialchars($item['link']) ?>">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-warning">
                                                <i class="fas fa-user-clock text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">
                                                <?= htmlspecialchars($item['grade']) ?><?= $item['sy'] ? ' &middot; S.Y. ' . htmlspecialchars($item['sy']) : '' ?>
                                            </div>
                                            <span class="font-weight-bold"><?= htmlspecialchars($item['name']) ?></span>
                                            <div class="small text-warning">Awaiting approval / rejection</div>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (!empty($pendingData['total'])): ?>
                                <a class="dropdown-item text-center small text-gray-500" href="admn_dashboard.php">Go to Dashboard</a>
                                <?php endif; ?>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

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