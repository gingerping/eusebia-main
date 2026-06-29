<?php
error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);
session_start();
require('classes/main.class.php');

$userdetails = $eusebia->get_userdata();
if (!$userdetails || $userdetails['role'] !== 'staff') {
    header('Location: login.php'); exit();
}

$adviser_grade   = $userdetails['adviser_grade']   ?? '';
$subject_handled = $userdetails['subject_handled'] ?? '';
$subject_grades  = $userdetails['subject_grades']  ?? '';
$subjects        = array_filter(array_map('trim', explode(',', $subject_handled)));
$subject_list    = array_filter(array_map('trim', explode(',', $subject_grades)));

// Grade → table/strand config
$grade_url_map = [
    'Grade 7'            => ['url'=>'staff_students.php?grade=7',                'table'=>'tbl_seven',  'strand'=>null],
    'Grade 8'            => ['url'=>'staff_students.php?grade=8',                'table'=>'tbl_eight',  'strand'=>null],
    'Grade 9'            => ['url'=>'staff_students.php?grade=9',                'table'=>'tbl_nine',   'strand'=>null],
    'Grade 10'           => ['url'=>'staff_students.php?grade=10',               'table'=>'tbl_ten',    'strand'=>null],
    'Grade 11 - STEM'    => ['url'=>'staff_students.php?grade=11&strand=STEM',   'table'=>'tbl_eleven', 'strand'=>'STEM'],
    'Grade 11 - ABM'     => ['url'=>'staff_students.php?grade=11&strand=ABM',    'table'=>'tbl_eleven', 'strand'=>'ABM'],
    'Grade 11 - GAS'     => ['url'=>'staff_students.php?grade=11&strand=GAS',    'table'=>'tbl_eleven', 'strand'=>'GAS'],
    'Grade 11 - TVL-ICT' => ['url'=>'staff_students.php?grade=11&strand=TVL-ICT','table'=>'tbl_eleven', 'strand'=>'TVL-ICT'],
    'Grade 11 - TVL-HE'  => ['url'=>'staff_students.php?grade=11&strand=TVL-HE', 'table'=>'tbl_eleven', 'strand'=>'TVL-HE'],
    'Grade 12 - STEM'    => ['url'=>'staff_students.php?grade=12&strand=STEM',   'table'=>'tbl_twelve', 'strand'=>'STEM'],
    'Grade 12 - ABM'     => ['url'=>'staff_students.php?grade=12&strand=ABM',    'table'=>'tbl_twelve', 'strand'=>'ABM'],
    'Grade 12 - GAS'     => ['url'=>'staff_students.php?grade=12&strand=GAS',    'table'=>'tbl_twelve', 'strand'=>'GAS'],
    'Grade 12 - TVL-ICT' => ['url'=>'staff_students.php?grade=12&strand=TVL-ICT','table'=>'tbl_twelve', 'strand'=>'TVL-ICT'],
    'Grade 12 - TVL-HE'  => ['url'=>'staff_students.php?grade=12&strand=TVL-HE', 'table'=>'tbl_twelve', 'strand'=>'TVL-HE'],
];

// Count students in assigned class
$adviser_count = 0;
if ($adviser_grade && isset($grade_url_map[$adviser_grade])) {
    $cfg  = $grade_url_map[$adviser_grade];
    $conn = $eusebia->openConn();
    try {
        // Check if table has enrollment_status
        $probe = $conn->prepare("SELECT enrollment_status FROM `{$cfg['table']}` LIMIT 1");
        $probe->execute();
        $has_status = true;
    } catch (PDOException $e) { $has_status = false; }

    $status_clause = $has_status ? " AND enrollment_status='Approved'" : "";
    $where = "(is_archived=0 OR is_archived IS NULL){$status_clause}";
    $params = [];
    if ($cfg['strand']) { $where .= " AND course=?"; $params[] = $cfg['strand']; }

    try {
        $s = $conn->prepare("SELECT COUNT(*) FROM `{$cfg['table']}` WHERE {$where}");
        $s->execute($params);
        $adviser_count = (int)$s->fetchColumn();
    } catch (PDOException $e) { $adviser_count = 0; }
}
?>
<?php include('staff_sidebar_start.php'); ?>

<div class="container-fluid">

    <!-- Welcome Banner -->
    <div class="card mb-4 border-0 shadow-sm" style="background:linear-gradient(135deg,#155724,#1e7e34);border-radius:16px;">
        <div class="card-body py-4 px-4 text-white">
            <div class="d-flex align-items-center">
                <div class="mr-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-0 font-weight-bold">
                        Welcome, <?= htmlspecialchars(($userdetails['lname'] ?? '') . ', ' . ($userdetails['fname'] ?? '')) ?>!
                    </h4>
                    <p class="mb-1 mt-1" style="opacity:.85;">
                        <i class="fas fa-id-badge mr-1"></i><?= htmlspecialchars($userdetails['position'] ?? 'Teacher') ?>
                        &nbsp;|&nbsp;
                        <i class="fas fa-book mr-1"></i>
                        <?= $subject_handled ? htmlspecialchars($subject_handled) : '<em>No subjects assigned</em>' ?>
                    </p>
                    <?php if ($adviser_grade): ?>
                    <span class="badge" style="background:#ffd700;color:#0b2b5c;font-size:.85rem;padding:5px 12px;">
                        <i class="fas fa-star mr-1"></i>Adviser: <?= htmlspecialchars($adviser_grade) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$adviser_grade): ?>
    <!-- No assignment notice -->
    <div class="alert alert-warning shadow-sm" style="border-radius:12px;">
        <h5 class="mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>No Class Assigned Yet</h5>
        <p class="mb-0">You have not been assigned an advisory class or subjects yet. Please contact your administrator.</p>
    </div>

    <?php else: ?>
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">My Advisory Class</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $adviser_count ?> students</div>
                            <small class="text-muted"><?= htmlspecialchars($adviser_grade) ?></small>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subjects Handled</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= count($subjects) ?: '—' ?></div>
                            <small class="text-muted"><?= $subject_handled ? htmlspecialchars($subject_handled) : 'None assigned' ?></small>
                        </div>
                        <div class="col-auto"><i class="fas fa-book-open fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">School Year</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= date('Y') . '-' . (date('Y')+1) ?></div>
                            <small class="text-muted">Current SY</small>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advisory Class Card -->
    <?php if ($adviser_grade && isset($grade_url_map[$adviser_grade])): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header font-weight-bold text-white py-2" style="background:linear-gradient(135deg,#0b2b5c,#0f3b7a);">
            <i class="fas fa-star mr-1" style="color:#ffd700;"></i> Advisory Class
        </div>
        <div class="card-body text-center py-3">
            <h5 class="font-weight-bold mb-2"><?= htmlspecialchars($adviser_grade) ?></h5>
            <a href="<?= htmlspecialchars($grade_url_map[$adviser_grade]['url']) ?>" class="btn btn-warning shadow-sm" style="border-radius:40px;padding:.6rem 2rem;">
                <i class="fas fa-star mr-2"></i> View Advisory Students
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Subject Grade Cards -->
    <?php if (!empty($subject_list)): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header font-weight-bold text-white py-2" style="background:linear-gradient(135deg,#0b2b5c,#0f3b7a);">
            <i class="fas fa-book mr-1"></i> My Subject Classes
        </div>
        <div class="card-body">
            <div class="row">
            <?php foreach ($subject_list as $sg):
                if (!isset($grade_url_map[$sg])) continue;
                $is_adv = ($sg === $adviser_grade);
            ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="<?= htmlspecialchars($grade_url_map[$sg]['url']) ?>"
                   class="btn btn-block shadow-sm <?= $is_adv ? 'btn-warning' : 'btn-primary' ?>"
                   style="border-radius:10px;font-size:.85rem;padding:.6rem;">
                    <i class="fas fa-users mr-1"></i> <?= htmlspecialchars($sg) ?>
                    <?php if ($is_adv): ?>
                    <br><small style="color:#0b2b5c;"><i class="fas fa-star"></i> Advisory</small>
                    <?php endif; ?>
                </a>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</div>

<?php include('dashboard_sidebar_end.php'); ?>