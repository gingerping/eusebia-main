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
$subject_list    = array_filter(array_map('trim', explode(',', $subject_grades)));

// Per-table config
$grade_config = [
    7  => ['table'=>'tbl_seven',  'label'=>'Grade 7',  'has_course'=>false, 'has_status'=>true,  'strand'=>null],
    8  => ['table'=>'tbl_eight',  'label'=>'Grade 8',  'has_course'=>false, 'has_status'=>true,  'strand'=>null],
    9  => ['table'=>'tbl_nine',   'label'=>'Grade 9',  'has_course'=>true,  'has_status'=>true,  'strand'=>null],
    10 => ['table'=>'tbl_ten',    'label'=>'Grade 10', 'has_course'=>true,  'has_status'=>false, 'strand'=>null],
    11 => ['table'=>'tbl_eleven', 'label'=>'Grade 11', 'has_course'=>true,  'has_status'=>false, 'strand'=>null],
    12 => ['table'=>'tbl_twelve', 'label'=>'Grade 12', 'has_course'=>true,  'has_status'=>true,  'strand'=>null],
];

$grade  = isset($_GET['grade'])  ? (int)$_GET['grade'] : 0;
$strand = isset($_GET['strand']) ? trim($_GET['strand']) : '';

// Build the key the way adviser_grade is stored e.g. "Grade 7" or "Grade 11 - STEM"
$requested_key = $grade <= 10 ? "Grade {$grade}" : "Grade {$grade} - {$strand}";

// BLOCK ACCESS: allow only if matches advisory class OR is in subject_grades list
$is_adviser   = ($adviser_grade === $requested_key);
$is_subject   = in_array($requested_key, $subject_list);
if (!$is_adviser && !$is_subject) {
    header('Location: staff_dashboard.php'); exit();
}

if (!isset($grade_config[$grade])) {
    header('Location: staff_dashboard.php'); exit();
}

$cfg   = $grade_config[$grade];
$table = $cfg['table'];
$label = $cfg['label'] . ($strand ? ' — ' . $strand : '');

$conn   = $eusebia->openConn();
$where  = ["(is_archived=0 OR is_archived IS NULL)"];
$params = [];

if ($cfg['has_status'])                                              $where[] = "enrollment_status='Approved'";
if ($cfg['has_course'] && in_array($grade,[11,12]) && $strand!=='') { $where[] = "course=?"; $params[] = $strand; }

$sql = "SELECT * FROM `{$table}` WHERE " . implode(' AND ', $where) . " ORDER BY lname ASC, fname ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
}

$is_shs = in_array($grade, [11,12]);
?>
<?php include('staff_sidebar_start.php'); ?>

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-users mr-2 text-success"></i><?= htmlspecialchars($label) ?> — Student List
            </h4>
            <small class="text-muted">
                <?= $cfg['has_status'] ? 'Approved enrollees' : 'All enrollees' ?>
                &nbsp;|&nbsp; <?= count($students) ?> student(s)
                <?php if ($is_adviser): ?>
                &nbsp;<span class="badge badge-warning"><i class="fas fa-star mr-1"></i>Advisory Class</span>
                <?php else: ?>
                &nbsp;<span class="badge badge-primary"><i class="fas fa-book mr-1"></i>Subject Class</span>
                <?php endif; ?>
            </small>
        </div>
        <a href="staff_dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($subject_handled): ?>
    <div class="alert alert-success py-2 mb-3" style="border-radius:10px;">
        <i class="fas fa-book mr-1"></i>
        <strong>Your Subjects:</strong> <?= htmlspecialchars($subject_handled) ?>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header py-2 d-flex align-items-center justify-content-between"
             style="background:linear-gradient(135deg,#155724,#1e7e34);">
            <span class="text-white font-weight-bold">
                <i class="fas fa-table mr-1"></i> <?= htmlspecialchars($label) ?> Enrollees
            </span>
            <button class="btn btn-warning btn-sm py-0" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm mb-0" id="studentsTable" style="font-size:.84rem;">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>LRN</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>M.I.</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Birthdate</th>
                            <th>Contact</th>
                            <th>School Year</th>
                            <?php if ($is_shs): ?><th>Strand</th><?php endif; ?>
                            <?php if ($cfg['has_status']): ?><th>Status</th><?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php if ($students): $r = 1; foreach ($students as $st): ?>
                        <tr>
                            <td><?= $r++ ?></td>
                            <td><code style="font-size:.8rem;"><?= htmlspecialchars($st['lrn']) ?></code></td>
                            <td class="text-left font-weight-bold"><?= htmlspecialchars($st['lname']) ?></td>
                            <td class="text-left"><?= htmlspecialchars($st['fname']) ?></td>
                            <td><?= htmlspecialchars($st['mi']) ?></td>
                            <td><?= htmlspecialchars($st['sex']) ?></td>
                            <td><?= $st['age'] ?></td>
                            <td><?= htmlspecialchars($st['bdate']) ?></td>
                            <td><?= htmlspecialchars($st['contact']) ?></td>
                            <td><?= htmlspecialchars($st['sy']) ?></td>
                            <?php if ($is_shs): ?>
                            <td><span class="badge badge-info"><?= htmlspecialchars($st['course'] ?? '') ?></span></td>
                            <?php endif; ?>
                            <?php if ($cfg['has_status']): ?>
                            <td>
                                <?php $s=$st['enrollment_status']??'Pending';
                                      $sc=$s==='Approved'?'success':($s==='Rejected'?'danger':'warning'); ?>
                                <span class="badge badge-<?= $sc ?>"><?= $s ?></span>
                            </td>
                            <?php endif; ?>
                            <td>
                                <button class="btn btn-primary btn-sm py-0 view-btn"
                                    data-lrn="<?= htmlspecialchars($st['lrn']) ?>"
                                    data-name="<?= htmlspecialchars($st['lname'].', '.$st['fname'].' '.$st['mi']) ?>"
                                    data-bdate="<?= htmlspecialchars($st['bdate']) ?>"
                                    data-sex="<?= htmlspecialchars($st['sex']) ?>"
                                    data-age="<?= $st['age'] ?>"
                                    data-address="<?= htmlspecialchars($st['current_address'] ?? '') ?>"
                                    data-contact="<?= htmlspecialchars($st['contact']) ?>"
                                    data-email="<?= htmlspecialchars($st['email'] ?? '') ?>"
                                    data-sy="<?= htmlspecialchars($st['sy']) ?>"
                                    data-father="<?= htmlspecialchars(trim(($st['ffname']??'').' '.($st['fmi']??'').' '.($st['flname']??''))) ?>"
                                    data-fcontact="<?= htmlspecialchars($st['contact_f'] ?? '') ?>"
                                    data-mother="<?= htmlspecialchars(trim(($st['mfname']??'').' '.($st['mmi']??'').' '.($st['mlname']??''))) ?>"
                                    data-mcontact="<?= htmlspecialchars($st['contact_m'] ?? '') ?>"
                                    data-lsa="<?= htmlspecialchars($st['lsa'] ?? '') ?>"
                                    data-lglc="<?= htmlspecialchars($st['lglc'] ?? '') ?>"
                                    data-strand="<?= htmlspecialchars($st['course'] ?? '') ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="13" class="text-center text-muted py-4">No students found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- VIEW STUDENT MODAL -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#155724,#1e7e34);">
                <h5 class="modal-title"><i class="fas fa-id-card mr-2"></i>Student Profile</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless" style="font-size:.88rem;">
                            <tr><th class="text-muted" width="40%">Full Name</th><td id="v_name" class="font-weight-bold"></td></tr>
                            <tr><th class="text-muted">LRN</th><td><code id="v_lrn"></code></td></tr>
                            <tr><th class="text-muted">Birthdate</th><td id="v_bdate"></td></tr>
                            <tr><th class="text-muted">Sex</th><td id="v_sex"></td></tr>
                            <tr><th class="text-muted">Age</th><td id="v_age"></td></tr>
                            <tr><th class="text-muted">School Year</th><td id="v_sy"></td></tr>
                            <tr><th class="text-muted">Strand</th><td id="v_strand"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless" style="font-size:.88rem;">
                            <tr><th class="text-muted" width="40%">Contact</th><td id="v_contact"></td></tr>
                            <tr><th class="text-muted">Email</th><td id="v_email"></td></tr>
                            <tr><th class="text-muted">Address</th><td id="v_address"></td></tr>
                            <tr><th class="text-muted">Last School</th><td id="v_lsa"></td></tr>
                            <tr><th class="text-muted">Last Grade</th><td id="v_lglc"></td></tr>
                        </table>
                    </div>
                </div>
                <hr class="my-2">
                <div class="row">
                    <div class="col-md-6">
                        <p class="font-weight-bold text-success mb-1"><i class="fas fa-male mr-1"></i>Father</p>
                        <p id="v_father" class="mb-0"></p>
                        <small class="text-muted" id="v_fcontact"></small>
                    </div>
                    <div class="col-md-6">
                        <p class="font-weight-bold text-danger mb-1"><i class="fas fa-female mr-1"></i>Mother</p>
                        <p id="v_mother" class="mb-0"></p>
                        <small class="text-muted" id="v_mcontact"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button class="btn btn-success btn-sm" onclick="window.print()"><i class="fas fa-print mr-1"></i>Print</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function(){
    $('#studentsTable').DataTable({ pageLength: 20, order: [[2,'asc']] });
});

// Outside document.ready so DataTable rows are included
$(document).on('click', '.view-btn', function(){
    // Use getAttribute to avoid jQuery data() caching/parsing issues
    const el = this;
    document.getElementById('v_name').textContent    = el.getAttribute('data-name')     || '';
    document.getElementById('v_lrn').textContent     = el.getAttribute('data-lrn')      || '';
    document.getElementById('v_bdate').textContent   = el.getAttribute('data-bdate')    || '';
    document.getElementById('v_sex').textContent     = el.getAttribute('data-sex')      || '';
    document.getElementById('v_age').textContent     = el.getAttribute('data-age')      || '';
    document.getElementById('v_sy').textContent      = el.getAttribute('data-sy')       || '';
    document.getElementById('v_strand').textContent  = el.getAttribute('data-strand')   || '—';
    document.getElementById('v_contact').textContent = el.getAttribute('data-contact')  || '';
    document.getElementById('v_email').textContent   = el.getAttribute('data-email')    || '';
    document.getElementById('v_address').textContent = el.getAttribute('data-address')  || '';
    document.getElementById('v_lsa').textContent     = el.getAttribute('data-lsa')      || '';
    document.getElementById('v_lglc').textContent    = el.getAttribute('data-lglc')     || '';
    document.getElementById('v_father').textContent  = el.getAttribute('data-father')   || '';
    document.getElementById('v_mother').textContent  = el.getAttribute('data-mother')   || '';
    const fc = el.getAttribute('data-fcontact');
    const mc = el.getAttribute('data-mcontact');
    document.getElementById('v_fcontact').textContent = fc ? 'Contact: ' + fc : '';
    document.getElementById('v_mcontact').textContent = mc ? 'Contact: ' + mc : '';
    $('#viewStudentModal').modal('show');
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>