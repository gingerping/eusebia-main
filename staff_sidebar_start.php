<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0b2b5c">
    <link rel="apple-touch-icon" href="icons/pwa/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="icons/pwa/icon-192x192.png">
    <title>EPAMNHS | Teacher Portal</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        #accordionSidebar { background: linear-gradient(135deg,#0b2b5c 0%,#0f3b7a 100%) !important; }
        #accordionSidebar .sidebar-brand,
        #accordionSidebar .nav-item .nav-link { color: rgba(255,255,255,.8) !important; }
        #accordionSidebar .nav-item .nav-link:hover,
        #accordionSidebar .nav-item.active .nav-link { color:#fff !important; background:rgba(255,255,255,.12) !important; }
        #accordionSidebar .sidebar-heading { color:rgba(255,255,255,.5) !important; }
        #accordionSidebar hr.sidebar-divider { border-color:rgba(255,255,255,.15) !important; }
        #sidebarToggle { background:rgba(255,255,255,.2) !important; }
        #sidebarToggle:hover { background:rgba(255,255,255,.35) !important; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background:linear-gradient(135deg,#0b2b5c 0%,#0f3b7a 100%)!important;">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="staff_dashboard.php">
            <div class="sidebar-brand-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="sidebar-brand-text mx-2">Teacher Portal</div>
        </a>
        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="staff_dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>

        <?php
        $adviser_grade  = $userdetails['adviser_grade']  ?? '';
        $subject_grades = $userdetails['subject_grades'] ?? '';
        $subject_list   = array_filter(array_map('trim', explode(',', $subject_grades)));

        $grade_url_map = [
            'Grade 7'            => 'staff_students.php?grade=7',
            'Grade 8'            => 'staff_students.php?grade=8',
            'Grade 9'            => 'staff_students.php?grade=9',
            'Grade 10'           => 'staff_students.php?grade=10',
            'Grade 11 - STEM'    => 'staff_students.php?grade=11&strand=STEM',
            'Grade 11 - ABM'     => 'staff_students.php?grade=11&strand=ABM',
            'Grade 11 - GAS'     => 'staff_students.php?grade=11&strand=GAS',
            'Grade 11 - TVL-ICT' => 'staff_students.php?grade=11&strand=TVL-ICT',
            'Grade 11 - TVL-HE'  => 'staff_students.php?grade=11&strand=TVL-HE',
            'Grade 12 - STEM'    => 'staff_students.php?grade=12&strand=STEM',
            'Grade 12 - ABM'     => 'staff_students.php?grade=12&strand=ABM',
            'Grade 12 - GAS'     => 'staff_students.php?grade=12&strand=GAS',
            'Grade 12 - TVL-ICT' => 'staff_students.php?grade=12&strand=TVL-ICT',
            'Grade 12 - TVL-HE'  => 'staff_students.php?grade=12&strand=TVL-HE',
        ];
        ?>

        <?php if ($adviser_grade && isset($grade_url_map[$adviser_grade])): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Advisory Class</div>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= $grade_url_map[$adviser_grade] ?>">
                <i class="fas fa-fw fa-star" style="color:#ffd700;"></i>
                <span><?= htmlspecialchars($adviser_grade) ?> <span class="badge badge-warning ml-1">Adviser</span></span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (!empty($subject_list)): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Subject Classes</div>
        <?php foreach ($subject_list as $sg):
            if (!isset($grade_url_map[$sg])) continue;
            if ($sg === $adviser_grade) continue; // skip if same as advisory
        ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= $grade_url_map[$sg] ?>">
                <i class="fas fa-fw fa-users"></i>
                <span><?= htmlspecialchars($sg) ?></span>
            </a>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!$adviser_grade && empty($subject_list)): ?>
        <hr class="sidebar-divider">
        <li class="nav-item">
            <span class="nav-link text-warning" style="opacity:.7;cursor:default;">
                <i class="fas fa-fw fa-exclamation-circle"></i>
                <span>No class assigned</span>
            </span>
        </li>
        <?php endif; ?>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Account</div>
        <li class="nav-item">
            <a class="nav-link" href="staff_changepass.php">
                <i class="fas fa-fw fa-key"></i><span>Change Password</span>
            </a>
        </li>
        <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </li>

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item d-none d-lg-flex align-items-center mr-2">
                        <span class="text-gray-600 small">
                            <i class="fas fa-chalkboard-teacher mr-1 text-success"></i>
                            <?= htmlspecialchars(($userdetails['lname'] ?? '') . ', ' . ($userdetails['fname'] ?? '')) ?>
                            &nbsp;<span class="badge badge-success"><?= htmlspecialchars($userdetails['position'] ?? 'Teacher') ?></span>
                        </span>
                    </li>
                </ul>
            </nav>