<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    require('classes/student.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    $school_name = 'Eusebia Paz Arroyo Memorial National High School';
    $school_year = $_GET['sy']   ?? '';
    $grade       = $_GET['grade'] ?? '';
    $do_print    = isset($_GET['print']);

    $grade_map = [
        'seven'  => ['label' => 'Grade 7',  'table' => 'tbl_seven',  'shs' => false],
        'eight'  => ['label' => 'Grade 8',  'table' => 'tbl_eight',  'shs' => false],
        'nine'   => ['label' => 'Grade 9',  'table' => 'tbl_nine',   'shs' => false],
        'ten'    => ['label' => 'Grade 10', 'table' => 'tbl_ten',    'shs' => false],
        'eleven' => ['label' => 'Grade 11', 'table' => 'tbl_eleven', 'shs' => true],
        'twelve' => ['label' => 'Grade 12', 'table' => 'tbl_twelve', 'shs' => true],
    ];

    // Get all available school years across all tables for the filter dropdown
    $all_sy = [];
    $conn   = $eusebia->openConn();
    foreach ($grade_map as $g => $info) {
        $r = $conn->query("SELECT DISTINCT sy FROM {$info['table']} WHERE is_archived = 0 OR is_archived IS NULL ORDER BY sy DESC");
        foreach ($r->fetchAll(PDO::FETCH_COLUMN) as $s) {
            if ($s) $all_sy[$s] = true;
        }
    }
    krsort($all_sy);
    $all_sy = array_keys($all_sy);

    // Fetch students
    $students = [];
    $grade_info = null;
    if ($grade && isset($grade_map[$grade])) {
        $grade_info = $grade_map[$grade];
        $table = $grade_info['table'];
        try { $conn->exec("ALTER TABLE $table ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
        if ($school_year) {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE (is_archived = 0 OR is_archived IS NULL) AND enrollment_status = 'Approved' AND sy = ? ORDER BY lname, fname");
            $stmt->execute([$school_year]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE (is_archived = 0 OR is_archived IS NULL) AND enrollment_status = 'Approved' ORDER BY lname, fname");
            $stmt->execute();
        }
        $students = $stmt->fetchAll();
    }

    // Group SHS by course
    $grouped = [];
    if ($grade_info && $grade_info['shs']) {
        foreach ($students as $s) {
            $course = trim($s['course'] ?? 'Unassigned');
            $grouped[$course][] = $s;
        }
        ksort($grouped);
    } else {
        $grouped['__all__'] = $students;
    }
?>
<?php if (!$do_print): ?>
<?php include('dashboard_sidebar_start.php'); ?>
<style>
.cl-header { background: linear-gradient(135deg,#0b2b5c,#0f3b7a); color:#fff; padding:18px 24px; border-radius:8px; margin-bottom:24px; }
.filter-card { border-radius:10px; }
.preview-badge { font-size:12px; padding:4px 12px; border-radius:20px; }
.print-btn { background:linear-gradient(135deg,#0b2b5c,#0f3b7a); border:none; color:#fff; font-weight:700; font-size:14px; padding:10px 28px; border-radius:10px; transition:opacity .2s; }
.print-btn:hover { opacity:.85; color:#fff; }
.count-pill { background:#eaf0fb; color:#0b2b5c; font-weight:700; font-size:13px; padding:5px 16px; border-radius:20px; border:1px solid #b3c6e0; }
.strand-section { margin-bottom: 10px; }
</style>
<div class="container-fluid">
    <div class="cl-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1"><i class="fas fa-print mr-2"></i>Class List</h4>
            <small class="opacity-75">Generate a print-ready enrollment list per grade level.</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="admn_classlist.php" class="form-inline flex-wrap" style="gap:12px;">
                <div class="form-group mr-3 mb-2">
                    <label class="font-weight-bold mr-2">Grade Level</label>
                    <select name="grade" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($grade_map as $val => $info): ?>
                            <option value="<?= $val ?>" <?= $grade === $val ? 'selected' : '' ?>><?= $info['label'] ?></option>
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
                    <?php if ($grade && !empty($students)): ?>
                        <a href="admn_classlist.php?grade=<?= $grade ?>&sy=<?= urlencode($school_year) ?>&print=1"
                           target="_blank" class="print-btn btn">
                            <i class="fas fa-print mr-1"></i> Print / Save PDF
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($grade && $grade_info): ?>
    <!-- Preview -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i>
                <?= $grade_info['label'] ?> — <?= $school_year ?: 'All School Years' ?>
            </h6>
            <span class="count-pill"><?= count($students) ?> student<?= count($students) != 1 ? 's' : '' ?></span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($students)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#ddd;"></i>
                    <p>No students found for this selection.</p>
                </div>
            <?php else: ?>
                <?php foreach ($grouped as $course => $rows): ?>
                    <?php if ($grade_info['shs']): ?>
                        <div class="strand-section">
                        <div class="px-3 pt-3">
                            <span class="badge badge-primary px-3 py-2" style="font-size:13px; background:#0b2b5c;"><?= htmlspecialchars($course) ?> — <?= count($rows) ?> student<?= count($rows)!=1?'s':'' ?></span>
                        </div>
                        </div>
                    <?php endif; ?>
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
                                    <?php if ($grade_info['shs']): ?><th>Strand</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n=1; foreach ($rows as $s): ?>
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
                                    <?php if ($grade_info['shs']): ?><td><?= htmlspecialchars($s['course']??'') ?></td><?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($students)): ?>
        <div class="card-footer text-muted small">
            <?= count($students) ?> student<?= count($students)!=1?'s':'' ?> · <?= $grade_info['label'] ?> · <?= $school_year ?: 'All Years' ?>
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
<title>Class List — <?= $grade_info ? $grade_info['label'] : '' ?> <?= htmlspecialchars($school_year) ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; background: #fff; }

    /* Screen-only toolbar */
    .screen-toolbar {
        display: flex; align-items: center; gap: 12px;
        background: #0b2b5c; color: #fff; padding: 10px 20px;
        position: sticky; top: 0; z-index: 100;
    }
    .screen-toolbar button {
        background: #fff; color: #0b2b5c; border: none; font-weight: 700;
        font-size: 13px; padding: 7px 18px; border-radius: 6px; cursor: pointer;
    }
    .screen-toolbar button:hover { background: #e0eaf8; }
    .screen-toolbar .tb-info { font-size: 13px; opacity: .85; }

    .print-page {
        width: 215.9mm; /* Letter */
        min-height: 279.4mm;
        margin: 20px auto;
        padding: 12mm 14mm;
        background: #fff;
        box-shadow: 0 2px 18px rgba(0,0,0,.12);
    }

    /* School header */
    .doc-header { text-align: center; margin-bottom: 12px; border-bottom: 2.5px solid #0b2b5c; padding-bottom: 10px; }
    .doc-header .republic { font-size: 8pt; text-transform: uppercase; letter-spacing: .06em; color: #444; }
    .doc-header .dept     { font-size: 8pt; text-transform: uppercase; letter-spacing: .05em; color: #444; margin-bottom: 4px; }
    .doc-header .school-name { font-size: 13pt; font-weight: 700; color: #0b2b5c; line-height: 1.3; }
    .doc-header .doc-title { font-size: 11pt; font-weight: 700; margin-top: 8px; text-transform: uppercase; letter-spacing: .08em; }
    .doc-header .doc-sub   { font-size: 9pt; color: #333; margin-top: 2px; }

    /* Meta row */
    .meta-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 9pt; }
    .meta-row span { border-bottom: 1px solid #000; padding: 0 6px 2px; display: inline-block; min-width: 120px; }
    .meta-label { font-weight: 700; margin-right: 4px; }

    /* Strand heading */
    .strand-heading { font-size: 10pt; font-weight: 700; background: #0b2b5c; color: #fff; padding: 4px 10px; margin: 12px 0 4px; border-radius: 3px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 16px; }
    thead th { background: #0b2b5c; color: #fff; padding: 5px 6px; text-align: center; border: 1px solid #0b2b5c; font-size: 8pt; }
    tbody td { padding: 4px 6px; border: 1px solid #bbb; text-align: center; }
    tbody tr:nth-child(even) td { background: #f4f7fb; }
    .td-name { text-align: left !important; }
    tbody tr:last-child td { border-bottom: 1.5px solid #0b2b5c; }

    /* Summary box */
    .summary-box { border: 1px solid #0b2b5c; border-radius: 4px; padding: 8px 14px; margin-bottom: 16px; font-size: 9pt; display: flex; gap: 30px; flex-wrap: wrap; }
    .summary-box .s-item { }
    .summary-box .s-label { color: #555; font-size: 8pt; }
    .summary-box .s-val   { font-size: 11pt; font-weight: 700; color: #0b2b5c; display: block; }

    /* Signature area */
    .sig-section { margin-top: 24px; display: flex; justify-content: space-between; gap: 20px; }
    .sig-block { flex: 1; text-align: center; }
    .sig-block .sig-line { border-top: 1.5px solid #000; margin: 28px 16px 3px; }
    .sig-block .sig-name  { font-weight: 700; font-size: 9.5pt; }
    .sig-block .sig-role  { font-size: 8.5pt; color: #444; }

    /* Footer */
    .doc-footer { text-align: center; margin-top: 18px; font-size: 7.5pt; color: #888; border-top: 1px solid #ddd; padding-top: 6px; }

    /* Print rules */
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

<!-- Screen toolbar -->
<div class="screen-toolbar">
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
    <button onclick="window.close()">✕ Close</button>
    <span class="tb-info">
        <?= $grade_info ? htmlspecialchars($grade_info['label']) : '' ?>
        <?= $school_year ? '· SY ' . htmlspecialchars($school_year) : '' ?>
        · <?= count($students) ?> student<?= count($students)!=1?'s':'' ?>
    </span>
</div>

<?php
// Determine how many pages/sections
// For SHS, each strand is a section; for JHS it's one section
$sections = $grouped;
$section_keys = array_keys($sections);
$today = date('F d, Y');

foreach ($section_keys as $si => $course_key):
    $rows = $sections[$course_key];
    $is_shs = $grade_info['shs'];
    $strand_label = $is_shs ? htmlspecialchars($course_key) : '';

    // Count by sex
    $male   = count(array_filter($rows, fn($r) => strtolower($r['sex']??'') === 'male'));
    $female = count(array_filter($rows, fn($r) => strtolower($r['sex']??'') === 'female'));
?>
<div class="print-page no-break">

    <!-- Header -->
    <div class="doc-header">
        <div class="republic">Republic of the Philippines</div>
        <div class="dept">Department of Education</div>
        <div class="school-name"><?= htmlspecialchars($school_name) ?></div>
        <div class="doc-title">Class Enrollment List</div>
        <div class="doc-sub">
            <?= $grade_info['label'] ?>
            <?= $is_shs ? ' — ' . htmlspecialchars($course_key) : '' ?>
            <?= $school_year ? ' &nbsp;|&nbsp; School Year ' . htmlspecialchars($school_year) : '' ?>
        </div>
    </div>

    <!-- Meta row -->
    <div class="meta-row">
        <div><span class="meta-label">Grade Level:</span><span><?= $grade_info['label'] ?><?= $is_shs ? ' – ' . htmlspecialchars($course_key) : '' ?></span></div>
        <div><span class="meta-label">School Year:</span><span><?= htmlspecialchars($school_year ?: '—') ?></span></div>
        <div><span class="meta-label">Date Printed:</span><span><?= $today ?></span></div>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <div class="s-item"><span class="s-label">Total Enrolled</span><span class="s-val"><?= count($rows) ?></span></div>
        <div class="s-item"><span class="s-label">Male</span><span class="s-val"><?= $male ?></span></div>
        <div class="s-item"><span class="s-label">Female</span><span class="s-val"><?= $female ?></span></div>
    </div>

    <!-- Student table -->
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
                <?php if ($is_shs): ?><th style="width:52px;">Strand</th><?php endif; ?>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php $n = 1; foreach ($rows as $s): ?>
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
                <?php if ($is_shs): ?><td><?= htmlspecialchars($s['course']??'') ?></td><?php endif; ?>
                <td class="td-name"><?= htmlspecialchars($s['current_address']??'') ?></td>
            </tr>
            <?php endforeach; ?>
            <!-- Empty rows for manual additions -->
            <?php for ($e = 0; $e < 3; $e++): ?>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><?php if($is_shs) echo '<td></td>'; ?><td></td></tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- Signatures -->
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

    <!-- Footer -->
    <div class="doc-footer">
        <?= htmlspecialchars($school_name) ?> &nbsp;·&nbsp; Printed: <?= $today ?> &nbsp;·&nbsp; <?= $grade_info['label'] ?><?= $school_year ? ' SY ' . htmlspecialchars($school_year) : '' ?>
    </div>

</div>
<?php endforeach; ?>

<script>
    // Auto-open print dialog if came from print link
    <?php if ($do_print): ?>
    window.onload = function() {
        // Small delay to let the page render fully
        setTimeout(function(){ window.print(); }, 400);
    };
    <?php endif; ?>
</script>
</body>
</html>
<?php endif; ?>