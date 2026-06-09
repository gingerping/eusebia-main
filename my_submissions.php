<?php 
error_reporting(E_ALL ^ E_WARNING);
include('classes/resident.class.php');

$userdetails = $eusebia->get_userdata();
$current_user_id = $userdetails['id_resident'];

// Fetch promotion requests for display
$my_promotion_requests = $eusebia->get_my_promotion_requests();

$connection = $eusebia->openConn();

$grades = [
    ['table' => 'tbl_seven',  'pk' => 'id_seven',  'label' => 'Grade 7',  'level' => 'Junior High - 1st Year'],
    ['table' => 'tbl_eight',  'pk' => 'id_eight',  'label' => 'Grade 8',  'level' => 'Junior High - 2nd Year'],
    ['table' => 'tbl_nine',   'pk' => 'id_nine',   'label' => 'Grade 9',  'level' => 'Junior High - 3rd Year'],
    ['table' => 'tbl_ten',    'pk' => 'id_ten',    'label' => 'Grade 10', 'level' => 'Junior High - 4th Year'],
    ['table' => 'tbl_eleven', 'pk' => 'id_eleven', 'label' => 'Grade 11', 'level' => 'Senior High - 11th'],
    ['table' => 'tbl_twelve', 'pk' => 'id_twelve', 'label' => 'Grade 12', 'level' => 'Senior High - 12th'],
];

$all_submissions = [];
foreach ($grades as $g) {
    try {
        $stmt = $connection->prepare(
            "SELECT * FROM {$g['table']} 
             WHERE id_resident = ? AND (is_archived = 0 OR is_archived IS NULL)"
        );
        $stmt->execute([$current_user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $row['grade_label'] = $g['label'];
            $row['grade_level'] = $g['level'];
            $all_submissions[] = $row;
        }
    } catch (PDOException $e) {
        // Skip tables that don't exist or have issues
        continue;
    }
}

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$current_date = $dt->format('l, F j, Y');

$total    = count($all_submissions);
$pending  = count(array_filter($all_submissions, fn($r) => strtolower($r['enrollment_status'] ?? 'pending') === 'pending'));
$approved = count(array_filter($all_submissions, fn($r) => strtolower($r['enrollment_status'] ?? '') === 'approved'));
$rejected = count(array_filter($all_submissions, fn($r) => strtolower($r['enrollment_status'] ?? '') === 'rejected'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0b2b5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EPAMNHS">
    <link rel="apple-touch-icon" href="icons/pwa/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="icons/pwa/icon-192x192.png">
    <title>My Submissions | EPAMHS Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
            background-attachment: fixed;
            color: #1a2c3e;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
            padding: 0.9rem 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700; font-size: 1.5rem; color: white !important;
        }
        .dropdown-toggle-custom {
            background: rgba(255,255,255,0.12);
            border-radius: 40px; padding: 8px 20px;
            border: 1px solid rgba(255,255,255,0.25);
            color: white !important; font-weight: 500; transition: all 0.2s;
        }
        .dropdown-toggle-custom:hover { background: rgba(255,255,255,0.25); }
        .dropdown-menu-custom {
            border: none; border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
            padding: 12px 6px; min-width: 210px;
            background: #ffffffdd; backdrop-filter: blur(12px);
        }
        .dropdown-item-custom {
            border-radius: 16px; padding: 10px 18px;
            font-weight: 500; transition: all 0.2s; color: #0b2b5c;
        }
        .dropdown-item-custom i { width: 28px; margin-right: 6px; }
        .dropdown-item-custom:hover { background: #eef2ff; transform: translateX(5px); }
        .dropdown-item-custom.active-page { background: #e8eeff; color: #0b2b5c; font-weight: 600; }

        .page-header {
            background: linear-gradient(135deg, #0b2b5c 0%, #1e5a88 100%);
            padding: 2.5rem 1rem 3rem; color: white; text-align: center; position: relative; overflow: hidden;
        }
        .page-header::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06), transparent 60%);
        }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; position: relative; }
        .page-header p { opacity: 0.8; position: relative; }
        .header-badge {
            background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
            border-radius: 80px; display: inline-block; padding: 0.3rem 1.2rem;
            font-size: 0.85rem; font-weight: 500; margin-bottom: 1rem;
        }

        .summary-section { margin-top: -1.5rem; padding: 0 1rem 1.5rem; }
        .summary-card {
            background: white; border-radius: 20px; padding: 1.2rem 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border-left: 5px solid #dee2e6; transition: transform 0.2s;
        }
        .summary-card:hover { transform: translateY(-3px); }
        .summary-card.pending  { border-left-color: #f59e0b; }
        .summary-card.approved { border-left-color: #10b981; }
        .summary-card.rejected { border-left-color: #ef4444; }
        .summary-card.total    { border-left-color: #6366f1; }
        .summary-number { font-size: 2rem; font-weight: 800; }
        .summary-label  { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }

        .submissions-section { padding: 1rem 1rem 4rem; }
        .submission-card {
            background: white; border-radius: 24px;
            box-shadow: 0 8px 30px -10px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05); overflow: hidden; transition: all 0.3s;
        }
        .submission-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px -12px rgba(0,0,0,0.18); }
        .card-header-custom {
            padding: 1.2rem 1.5rem; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #f1f5f9;
        }
        .grade-badge-card {
            background: linear-gradient(135deg, #0b2b5c, #1e5a88);
            color: white; border-radius: 12px; padding: 0.4rem 1rem;
            font-weight: 700; font-size: 0.9rem;
        }
        .status-badge {
            border-radius: 80px; padding: 0.35rem 1rem; font-weight: 600;
            font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;
        }
        .status-badge.pending  { background: #fef3c7; color: #92400e; }
        .status-badge.approved { background: #d1fae5; color: #065f46; }
        .status-badge.rejected { background: #fee2e2; color: #991b1b; }
        .card-body-custom { padding: 1.5rem; }
        .info-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;
        }
        .info-item label {
            display: block; font-size: 0.72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 3px;
        }
        .info-item span { font-weight: 600; color: #1e293b; font-size: 0.9rem; }
        .reject-reason-box {
            background: #fff5f5; border: 1px solid #fecaca; border-radius: 12px;
            padding: 0.9rem 1.2rem; margin-top: 1rem; font-size: 0.875rem; color: #7f1d1d;
        }

        .status-timeline {
            display: flex; align-items: center; margin-top: 1rem; padding: 0.75rem 0;
        }
        .timeline-step { flex: 1; text-align: center; position: relative; }
        .timeline-step::after {
            content: ''; position: absolute; top: 14px; left: 50%;
            width: 100%; height: 2px; background: #e2e8f0; z-index: 0;
        }
        .timeline-step:last-child::after { display: none; }
        .step-dot {
            width: 28px; height: 28px; border-radius: 50%; background: #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; position: relative; z-index: 1;
            color: #94a3b8; border: 2px solid #e2e8f0;
        }
        .step-dot.done   { background: #10b981; border-color: #10b981; color: white; }
        .step-dot.active { background: #f59e0b; border-color: #f59e0b; color: white; }
        .step-dot.failed { background: #ef4444; border-color: #ef4444; color: white; }
        .step-label { font-size: 0.68rem; font-weight: 600; color: #94a3b8; margin-top: 4px; }
        .step-label.done   { color: #059669; }
        .step-label.active { color: #d97706; }
        .step-label.failed { color: #dc2626; }

        .empty-state {
            text-align: center; padding: 4rem 1rem; background: white;
            border-radius: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .empty-icon { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; }
        .empty-state h4 { font-weight: 700; color: #334155; }
        .empty-state p  { color: #64748b; }
        .btn-go-enroll {
            background: linear-gradient(135deg, #0b2b5c, #1e5a88); color: white;
            border: none; border-radius: 80px; padding: 0.65rem 2rem; font-weight: 600;
            text-decoration: none; display: inline-block; transition: all 0.2s; margin-top: 1rem;
        }
        .btn-go-enroll:hover { transform: translateY(-2px); color: white; opacity: 0.9; }

        .filter-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .filter-btn {
            border: 1.5px solid #e2e8f0; border-radius: 80px; padding: 0.4rem 1.2rem;
            font-size: 0.85rem; font-weight: 600; cursor: pointer; background: white;
            color: #64748b; transition: all 0.2s;
        }
        .filter-btn:hover, .filter-btn.active { background: #0b2b5c; border-color: #0b2b5c; color: white; }

        .footer-custom {
            background: #0b1f33; color: #cddcec; padding: 2rem 1rem;
            text-align: center; font-size: 0.9rem;
            border-top-left-radius: 32px; border-top-right-radius: 32px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">
            <i class="bi bi-mortarboard-fill me-2"></i> EPAMHS Portal
        </a>
        <div class="dropdown ms-auto">
            <button class="btn dropdown-toggle-custom dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i> <?= htmlspecialchars($userdetails['surname'] . ', ' . $userdetails['firstname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item dropdown-item-custom" href="resident_homepage.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="dropdown-item dropdown-item-custom active-page" href="my_submissions.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-file-alt"></i> My Submissions</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="resident_profile.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-id-card"></i> My Profile</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="resident_changepass.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-key"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item dropdown-item-custom" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <div class="header-badge">
            <i class="fas fa-clipboard-list me-2"></i> Enrollment Tracker
        </div>
        <h1>My Form Submissions</h1>
        <p class="mt-2">Track the status of all your enrollment applications</p>
        <div class="mt-2" style="font-size:0.85rem; opacity:0.7;">
            <i class="far fa-calendar-alt me-1"></i> <?= $current_date ?>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-section">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="summary-card total">
                    <div class="summary-number" style="color:#6366f1"><?= $total ?></div>
                    <div class="summary-label">Total Submitted</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-card pending">
                    <div class="summary-number" style="color:#f59e0b"><?= $pending ?></div>
                    <div class="summary-label">Pending Review</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-card approved">
                    <div class="summary-number" style="color:#10b981"><?= $approved ?></div>
                    <div class="summary-label">Approved</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-card rejected">
                    <div class="summary-number" style="color:#ef4444"><?= $rejected ?></div>
                    <div class="summary-label">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submissions -->
<div class="submissions-section">
    <div class="container">

        <?php if (empty($all_submissions)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
            <h4>No Submissions Yet</h4>
            <p class="mt-2">You haven't submitted any enrollment forms yet.<br>Head back to the dashboard to choose your grade level and apply.</p>
            <a href="resident_homepage.php" class="btn-go-enroll">
                <i class="fas fa-arrow-right me-2"></i> Go to Dashboard
            </a>
        </div>

        <?php else: ?>

        <div class="filter-tabs">
            <button class="filter-btn active" onclick="filterCards('all', this)">All (<?= $total ?>)</button>
            <button class="filter-btn" onclick="filterCards('pending', this)">
                <i class="fas fa-clock me-1"></i> Pending (<?= $pending ?>)
            </button>
            <button class="filter-btn" onclick="filterCards('approved', this)">
                <i class="fas fa-check me-1"></i> Approved (<?= $approved ?>)
            </button>
            <button class="filter-btn" onclick="filterCards('rejected', this)">
                <i class="fas fa-times me-1"></i> Rejected (<?= $rejected ?>)
            </button>
        </div>

        <div class="row g-4" id="submissionsGrid">
            <?php foreach ($all_submissions as $sub):
                $status      = strtolower($sub['enrollment_status'] ?? 'pending');
                $statusLabel = ucfirst($status);
                $statusIcon  = $status === 'approved' ? 'fa-check-circle' : ($status === 'rejected' ? 'fa-times-circle' : 'fa-hourglass-half');
                $sy          = htmlspecialchars($sub['sy'] ?? 'N/A');
                $lrn         = htmlspecialchars($sub['lrn'] ?? 'N/A');
                $name        = htmlspecialchars(($sub['lname'] ?? '') . ', ' . ($sub['fname'] ?? '') . ' ' . ($sub['mi'] ?? ''));
                $course      = htmlspecialchars($sub['course'] ?? '');
                $rejectReason = htmlspecialchars($sub['reject_reason'] ?? '');
            ?>
            <div class="col-12 col-md-6 submission-item" data-status="<?= $status ?>">
                <div class="submission-card">
                    <div class="card-header-custom">
                        <div>
                            <div class="grade-badge-card"><?= htmlspecialchars($sub['grade_label']) ?></div>
                            <div class="mt-1" style="font-size:0.78rem; color:#94a3b8;">
                                <?= htmlspecialchars($sub['grade_level']) ?>
                            </div>
                        </div>
                        <div class="status-badge <?= $status ?>">
                            <i class="fas <?= $statusIcon ?>"></i> <?= $statusLabel ?>
                        </div>
                    </div>

                    <div class="card-body-custom">
                        <!-- Timeline -->
                        <div class="status-timeline">
                            <div class="timeline-step">
                                <div class="step-dot done"><i class="fas fa-paper-plane" style="font-size:0.6rem"></i></div>
                                <div class="step-label done">Submitted</div>
                            </div>
                            <div class="timeline-step">
                                <div class="step-dot <?= $status !== 'pending' ? 'done' : 'active' ?>">
                                    <i class="fas fa-search" style="font-size:0.6rem"></i>
                                </div>
                                <div class="step-label <?= $status !== 'pending' ? 'done' : 'active' ?>">Under Review</div>
                            </div>
                            <div class="timeline-step">
                                <div class="step-dot <?= $status === 'approved' ? 'done' : ($status === 'rejected' ? 'failed' : '') ?>">
                                    <i class="fas <?= $status === 'rejected' ? 'fa-times' : 'fa-check' ?>" style="font-size:0.6rem"></i>
                                </div>
                                <div class="step-label <?= $status === 'approved' ? 'done' : ($status === 'rejected' ? 'failed' : '') ?>">
                                    <?= $status === 'rejected' ? 'Rejected' : 'Approved' ?>
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="info-grid mt-3">
                            <div class="info-item">
                                <label><i class="fas fa-user me-1"></i> Full Name</label>
                                <span><?= $name ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-id-badge me-1"></i> LRN</label>
                                <span><?= $lrn ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar me-1"></i> School Year</label>
                                <span><?= $sy ?></span>
                            </div>
                            <?php if ($course): ?>
                            <div class="info-item">
                                <label><i class="fas fa-book me-1"></i> Strand / Course</label>
                                <span><?= $course ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($status === 'rejected' && $rejectReason): ?>
                        <div class="reject-reason-box mt-3">
                            <strong><i class="fas fa-exclamation-circle me-1" style="color:#ef4444"></i> Reason for Rejection:</strong><br>
                            <?= $rejectReason ?>
                        </div>
                        <?php elseif ($status === 'approved'): ?>
                        <div class="mt-3 p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                            <i class="fas fa-info-circle text-success me-1"></i>
                            <span style="color:#065f46; font-size:0.85rem; font-weight:500;">
                                Your enrollment is <strong>approved</strong>. Please visit the school to complete your requirements.
                            </span>
                        </div>
                        <?php elseif ($status === 'pending'): ?>
                        <div class="mt-3 p-3 rounded-3" style="background:#fffbeb; border:1px solid #fde68a;">
                            <i class="fas fa-info-circle" style="color:#b45309;"></i>
                            <span style="color:#92400e; font-size:0.85rem; font-weight:500;">
                                Your application is <strong>under review</strong>. You will be notified via email once a decision is made.
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="noFilterResult" class="empty-state mt-4" style="display:none;">
            <div class="empty-icon"><i class="fas fa-filter"></i></div>
            <h4>No Submissions Found</h4>
            <p>No submissions match the selected filter.</p>
        </div>

        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     PROMOTION REQUESTS SECTION
     ============================================================ -->
<?php if (!empty($my_promotion_requests)): ?>
<div class="container-fluid px-4 pb-4">
    <div class="card" style="border:none; border-radius:14px; box-shadow:0 2px 14px rgba(11,43,92,.10);">
        <div class="card-header" style="background:#0b2b5c; color:#fff; border-radius:14px 14px 0 0; font-weight:600;">
            <i class="fas fa-level-up-alt me-2"></i> My Promotion Requests
            <a href="promotion_request.php" class="btn btn-sm btn-light float-end" style="font-size:.82rem;">
                <i class="fas fa-plus me-1"></i> New Request
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th>#</th>
                            <th>From Grade</th>
                            <th>To Grade</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($my_promotion_requests as $i => $pr): ?>
                        <?php $st = strtolower($pr['status']); ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>Grade <?= htmlspecialchars($pr['from_grade']) ?></td>
                            <td>Grade <?= htmlspecialchars($pr['to_grade']) ?></td>
                            <td style="font-size:.85rem;"><?= htmlspecialchars($pr['submitted_at']) ?></td>
                            <td>
                                <span style="border-radius:20px; padding:3px 13px; font-size:.82rem; font-weight:600;
                                    <?= $st==='pending'  ? 'background:#fef3c7;color:#92400e;' :
                                       ($st==='approved' ? 'background:#d1fae5;color:#065f46;' :
                                                           'background:#fee2e2;color:#991b1b;') ?>">
                                    <?= htmlspecialchars($pr['status']) ?>
                                </span>
                            </td>
                            <td style="font-size:.85rem;">
                                <?php if ($st === 'approved'): ?>
                                    <i class="fas fa-check-circle text-success"></i> Promoted to Grade <?= htmlspecialchars($pr['to_grade']) ?>
                                <?php elseif ($st === 'rejected' && $pr['reject_reason']): ?>
                                    <i class="fas fa-times-circle text-danger"></i> <?= htmlspecialchars($pr['reject_reason']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Waiting for admin review…</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<?php endif; ?>
<!-- ============================================================ -->

<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0"><?= date('Y') ?> EPAMHS Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterCards(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const items = document.querySelectorAll('.submission-item');
    let visible = 0;
    items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });
    const noResult = document.getElementById('noFilterResult');
    if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
}
</script>
<script src="js/pwa.js"></script>
</body>
</html>