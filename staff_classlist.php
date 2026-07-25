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
    $subject_grades  = $userdetails['subject_grades']  ?? '';
    $subject_list    = array_filter(array_map('trim', explode(',', $subject_grades)));

    $school_name = 'Eusebia Paz Arroyo Memorial National High School';
    $school_year = $_GET['sy'] ?? '';
    $do_print    = isset($_GET['print']);

    // Full catalogue of grade/strand combinations, keyed the same way adviser_grade / subject_grades are stored.
    $all_classes = [
        'Grade 7'            => ['label'=>'Grade 7',            'table'=>'tbl_seven',  'shs'=>false, 'grade'=>7,  'strand'=>null],
        'Grade 8'            => ['label'=>'Grade 8',            'table'=>'tbl_eight',  'shs'=>false, 'grade'=>8,  'strand'=>null],
        'Grade 9'            => ['label'=>'Grade 9',            'table'=>'tbl_nine',   'shs'=>false, 'grade'=>9,  'strand'=>null],
        'Grade 10'           => ['label'=>'Grade 10',           'table'=>'tbl_ten',    'shs'=>false, 'grade'=>10, 'strand'=>null],
        'Grade 11 - STEM'    => ['label'=>'Grade 11 — STEM',    'table'=>'tbl_eleven', 'shs'=>true,  'grade'=>11, 'strand'=>'STEM'],
        'Grade 11 - ABM'     => ['label'=>'Grade 11 — ABM',     'table'=>'tbl_eleven', 'shs'=>true,  'grade'=>11, 'strand'=>'ABM'],
        'Grade 11 - GAS'     => ['label'=>'Grade 11 — GAS',     'table'=>'tbl_eleven', 'shs'=>true,  'grade'=>11, 'strand'=>'GAS'],
        'Grade 11 - TVL-ICT' => ['label'=>'Grade 11 — TVL-ICT', 'table'=>'tbl_eleven', 'shs'=>true,  'grade'=>11, 'strand'=>'TVL-ICT'],
        'Grade 11 - TVL-HE'  => ['label'=>'Grade 11 — TVL-HE',  'table'=>'tbl_eleven', 'shs'=>true,  'grade'=>11, 'strand'=>'TVL-HE'],
        'Grade 12 - STEM'    => ['label'=>'Grade 12 — STEM',    'table'=>'tbl_twelve', 'shs'=>true,  'grade'=>12, 'strand'=>'STEM'],
        'Grade 12 - ABM'     => ['label'=>'Grade 12 — ABM',     'table'=>'tbl_twelve', 'shs'=>true,  'grade'=>12, 'strand'=>'ABM'],
        'Grade 12 - GAS'     => ['label'=>'Grade 12 — GAS',     'table'=>'tbl_twelve', 'shs'=>true,  'grade'=>12, 'strand'=>'GAS'],
        'Grade 12 - TVL-ICT' => ['label'=>'Grade 12 — TVL-ICT', 'table'=>'tbl_twelve', 'shs'=>true,  'grade'=>12, 'strand'=>'TVL-ICT'],
        'Grade 12 - TVL-HE'  => ['label'=>'Grade 12 — TVL-HE',  'table'=>'tbl_twelve', 'shs'=>true,  'grade'=>12, 'strand'=>'TVL-HE'],
    ];

    // Only the classes this staff member actually teaches/advises can be selected.
    $allowed_keys = array_filter(array_unique(array_merge(
        $adviser_grade ? [$adviser_grade] : [],
        $subject_list
    )));
    $class_options = array_intersect_key($all_classes, array_flip($allowed_keys));

    $requested_key = $_GET['class'] ?? '';
    $class_info    = null;
    if ($requested_key && isset($class_options[$requested_key])) {
        $class_info = $class_options[$requested_key];
    }

    // Get available school years for this class only
    $all_sy = [];
    $conn   = $eusebia->openConn();
    if ($class_info) {
        $table = $class_info['table'];
        try { $conn->exec("ALTER TABLE $table ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
        $r = $conn->query("SELECT DISTINCT sy FROM {$table} WHERE (is_archived = 0 OR is_archived IS NULL) ORDER BY sy DESC");
        foreach ($r->fetchAll(PDO::FETCH_COLUMN) as $s) { if ($s) $all_sy[$s] = true; }
        krsort($all_sy);
        $all_sy = array_keys($all_sy);
    }

    // Fetch APPROVED students only
    $students = [];
    if ($class_info) {
        $table  = $class_info['table'];
        $where  = ["(is_archived = 0 OR is_archived IS NULL)", "enrollment_status = 'Approved'"];
        $params = [];
        if ($class_info['strand']) { $where[] = "course = ?"; $params[] = $class_info['strand']; }
        if ($school_year)          { $where[] = "sy = ?";     $params[] = $school_year; }
        $stmt = $conn->prepare("SELECT * FROM $table WHERE " . implode(' AND ', $where) . " ORDER BY lname, fname");
        $stmt->execute($params);
        $students = $stmt->fetchAll();
    }
?>
<?php if (!$do_print): ?>
<?php include('staff_sidebar_start.php'); ?>
<style>
.cl-header { background: linear-gradient(135deg,#155724,#1e7e34); color:#fff; padding:18px 24px; border-radius:8px; margin-bottom:24px; }
.filter-card { border-radius:10px; }
.print-btn { background:linear-gradient(135deg,#155724,#1e7e34); border:none; color:#fff; font-weight:700; font-size:14px; padding:10px 28px; border-radius:10px; transition:opacity .2s; }
.print-btn:hover { opacity:.85; color:#fff; }
.count-pill { background:#e9f7ef; color:#155724; font-weight:700; font-size:13px; padding:5px 16px; border-radius:20px; border:1px solid #b6e6c6; }
</style>
<div class="container-fluid">
    <div class="cl-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1"><i class="fas fa-print mr-2"></i>Class List</h4>
            <small class="opacity-75">Print-ready roster of approved enrollees for your class.</small>
        </div>
        <a href="staff_dashboard.php" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="staff_classlist.php" class="form-inline flex-wrap" style="gap:12px;">
                <div class="form-group mr-3 mb-2">
                    <label class="font-weight-bold mr-2">Class</label>
                    <select name="class" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($class_options as $key => $info): ?>
                            <option value="<?= htmlspecialchars($key) ?>" <?= $requested_key === $key ? 'selected' : '' ?>><?= $info['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="font-weight-bold mr-2">School Year</label>
                    <select name="sy" class="form-control">
                        <option value="">All Years</option>
                        <?php foreach ($all_sy as $sy): ?>
                            <option value="<?= htmlspecialchars($sy) ?>" <?= $school_year === $sy ? 'selected' : '' ?>><?= htmlspecialchars($sy) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter mr-1"></i> Generate
                    </button>
                    <?php if ($class_info && !empty($students)): ?>
                        <a href="staff_classlist.php?class=<?= urlencode($requested_key) ?>&sy=<?= urlencode($school_year) ?>&print=1"
                           target="_blank" class="print-btn btn">
                            <i class="fas fa-print mr-1"></i> Print / Save PDF
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($requested_key && $class_info): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list mr-1"></i>
                <?= $class_info['label'] ?> — <?= $school_year ?: 'All School Years' ?>
            </h6>
            <span class="count-pill"><?= count($students) ?> student<?= count($students) != 1 ? 's' : '' ?></span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($students)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#ddd;"></i>
                    <p>No approved students found for this selection.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>LRN</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>M.I.</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Birthday</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n=1; foreach ($students as $s): ?>
                            <tr>
                                <td><?= $n++ ?></td>
                                <td><?= htmlspecialchars($s['lrn']??'') ?></td>
                                <td><?= htmlspecialchars($s['lname']??'') ?></td>
                                <td><?= htmlspecialchars($s['fname']??'') ?></td>
                                <td><?= htmlspecialchars($s['mi']??'') ?></td>
                                <td><?= htmlspecialchars($s['sex']??'') ?></td>
                                <td><?= htmlspecialchars($s['age']??'') ?></td>
                                <td><?= htmlspecialchars($s['bdate']??'') ?></td>
                                <td><?= htmlspecialchars($s['contact']??'') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($students)): ?>
        <div class="card-footer text-muted small">
            <?= count($students) ?> student<?= count($students)!=1?'s':'' ?> · <?= $class_info['label'] ?> · <?= $school_year ?: 'All Years' ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php include('dashboard_sidebar_end.php'); ?>

<?php else: /* ===== PRINT VIEW ===== */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Class List — <?= $class_info ? htmlspecialchars($class_info['label']) : '' ?> <?= htmlspecialchars($school_year) ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; background: #fff; }

    .screen-toolbar {
        display: flex; align-items: center; gap: 12px;
        background: #155724; color: #fff; padding: 10px 20px;
        position: sticky; top: 0; z-index: 100;
    }
    .screen-toolbar button {
        background: #fff; color: #155724; border: none; font-weight: 700;
        font-size: 13px; padding: 7px 18px; border-radius: 6px; cursor: pointer;
    }
    .screen-toolbar button:hover { background: #e0f5e6; }
    .screen-toolbar .tb-info { font-size: 13px; opacity: .85; }

    .print-page {
        width: 215.9mm;
        min-height: 279.4mm;
        margin: 20px auto;
        padding: 12mm 14mm;
        background: #fff;
        box-shadow: 0 2px 18px rgba(0,0,0,.12);
    }

    .doc-header { text-align: center; margin-bottom: 12px; border-bottom: 2.5px solid #155724; padding-bottom: 10px; }
    .doc-header .republic { font-size: 8pt; text-transform: uppercase; letter-spacing: .06em; color: #444; }
    .doc-header .dept     { font-size: 8pt; text-transform: uppercase; letter-spacing: .05em; color: #444; margin-bottom: 4px; }
    .doc-header .school-name { font-size: 13pt; font-weight: 700; color: #155724; line-height: 1.3; }
    .doc-header .doc-title { font-size: 11pt; font-weight: 700; margin-top: 8px; text-transform: uppercase; letter-spacing: .08em; }
    .doc-header .doc-sub   { font-size: 9pt; color: #333; margin-top: 2px; }

    .meta-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 9pt; }
    .meta-row span { border-bottom: 1px solid #000; padding: 0 6px 2px; display: inline-block; min-width: 120px; }
    .meta-label { font-weight: 700; margin-right: 4px; }

    table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 16px; }
    thead th { background: #155724; color: #fff; padding: 5px 6px; text-align: center; border: 1px solid #155724; font-size: 8pt; }
    tbody td { padding: 4px 6px; border: 1px solid #bbb; text-align: center; }
    tbody tr:nth-child(even) td { background: #f2f9f4; }
    .td-name { text-align: left !important; }
    tbody tr:last-child td { border-bottom: 1.5px solid #155724; }

    .summary-box { border: 1px solid #155724; border-radius: 4px; padding: 8px 14px; margin-bottom: 16px; font-size: 9pt; display: flex; gap: 30px; flex-wrap: wrap; }
    .summary-box .s-label { color: #555; font-size: 8pt; }
    .summary-box .s-val   { font-size: 11pt; font-weight: 700; color: #155724; display: block; }

    .sig-section { margin-top: 24px; display: flex; justify-content: space-between; gap: 20px; }
    .sig-block { flex: 1; text-align: center; }
    .sig-block .sig-line { border-top: 1.5px solid #000; margin: 28px 16px 3px; }
    .sig-block .sig-name  { font-weight: 700; font-size: 9.5pt; }
    .sig-block .sig-role  { font-size: 8.5pt; color: #444; }

    .doc-footer { text-align: center; margin-top: 18px; font-size: 7.5pt; color: #888; border-top: 1px solid #ddd; padding-top: 6px; }

    @media print {
        .screen-toolbar { display: none !important; }
        .print-page { margin: 0; box-shadow: none; padding: 10mm 12mm; width: 100%; }
        body { background: #fff; }
        @page { size: Letter portrait; margin: 0; }
        .no-break { page-break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="screen-toolbar">
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
    <button onclick="window.close()">✕ Close</button>
    <span class="tb-info">
        <?= $class_info ? htmlspecialchars($class_info['label']) : '' ?>
        <?= $school_year ? '· SY ' . htmlspecialchars($school_year) : '' ?>
        · <?= count($students) ?> student<?= count($students)!=1?'s':'' ?>
    </span>
</div>

<?php
$today  = date('F d, Y');
$male   = count(array_filter($students, fn($r) => strtolower($r['sex']??'') === 'male'));
$female = count(array_filter($students, fn($r) => strtolower($r['sex']??'') === 'female'));
?>
<div class="print-page no-break">

    <div class="doc-header">
        <div class="republic">Republic of the Philippines</div>
        <div class="dept">Department of Education</div>
        <div class="school-name"><?= htmlspecialchars($school_name) ?></div>
        <div class="doc-title">Class Enrollment List</div>
        <div class="doc-sub">
            <?= $class_info ? htmlspecialchars($class_info['label']) : '' ?>
            <?= $school_year ? ' &nbsp;|&nbsp; School Year ' . htmlspecialchars($school_year) : '' ?>
        </div>
    </div>

    <div class="meta-row">
        <div><span class="meta-label">Class:</span><span><?= $class_info ? htmlspecialchars($class_info['label']) : '' ?></span></div>
        <div><span class="meta-label">School Year:</span><span><?= htmlspecialchars($school_year ?: '—') ?></span></div>
        <div><span class="meta-label">Date Printed:</span><span><?= $today ?></span></div>
    </div>

    <div class="summary-box">
        <div class="s-item"><span class="s-label">Total Enrolled</span><span class="s-val"><?= count($students) ?></span></div>
        <div class="s-item"><span class="s-label">Male</span><span class="s-val"><?= $male ?></span></div>
        <div class="s-item"><span class="s-label">Female</span><span class="s-val"><?= $female ?></span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:28px;">No.</th>
                <th style="width:120px;">Last Name</th>
                <th style="width:110px;">First Name</th>
                <th style="width:28px;">M.I.</th>
                <th style="width:100px;">LRN</th>
                <th style="width:44px;">Sex</th>
                <th style="width:28px;">Age</th>
                <th style="width:72px;">Birthday</th>
                <th style="width:88px;">Contact</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php $n = 1; foreach ($students as $s): ?>
            <tr>
                <td><?= $n++ ?></td>
                <td class="td-name"><?= htmlspecialchars(strtoupper($s['lname']??'')) ?></td>
                <td class="td-name"><?= htmlspecialchars($s['fname']??'') ?></td>
                <td><?= htmlspecialchars($s['mi']??'') ?></td>
                <td><?= htmlspecialchars($s['lrn']??'') ?></td>
                <td><?= htmlspecialchars($s['sex']??'') ?></td>
                <td><?= htmlspecialchars($s['age']??'') ?></td>
                <td><?= htmlspecialchars($s['bdate']??'') ?></td>
                <td><?= htmlspecialchars($s['contact']??'') ?></td>
                <td class="td-name"><?= htmlspecialchars($s['current_address']??'') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php for ($e = 0; $e < 3; $e++): ?>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="sig-section">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-name">________________________________</div>
            <div class="sig-role">Class Adviser / Teacher</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-name">________________________________</div>
            <div class="sig-role">Registrar</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-name">________________________________</div>
            <div class="sig-role">School Principal</div>
        </div>
    </div>

    <div class="doc-footer">
        <?= htmlspecialchars($school_name) ?> &nbsp;·&nbsp; Printed: <?= $today ?> &nbsp;·&nbsp; <?= $class_info ? htmlspecialchars($class_info['label']) : '' ?><?= $school_year ? ' SY ' . htmlspecialchars($school_year) : '' ?>
    </div>

</div>

<script>
    <?php if ($do_print): ?>
    window.onload = function() {
        setTimeout(function(){ window.print(); }, 400);
    };
    <?php endif; ?>
</script>
</body>
</html>
<?php endif; ?>
