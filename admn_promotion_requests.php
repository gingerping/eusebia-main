<?php
error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);
require('classes/student.class.php');

$userdetails = $eusebia->get_userdata();
$eusebia->validate_admin();

// Handle POSTs
$eusebia->admin_approve_promotion_request();
$eusebia->admin_reject_promotion_request();
$eusebia->admin_bulk_delete_promotion_requests();

$status_filter = $_GET['status'] ?? '';
$requests = $eusebia->admin_get_promotion_requests($status_filter);

$swal = $_SESSION['swal'] ?? null;
unset($_SESSION['swal']);
?>

<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .page-header { background:linear-gradient(135deg,#0b2b5c,#1565c0); color:#fff; border-radius:14px; padding:26px 30px; margin-bottom:24px; }
    .page-header h1 { font-size:1.6rem; font-weight:700; margin:0; }
    .tbl-card { border:none; border-radius:14px; box-shadow:0 2px 14px rgba(11,43,92,.10); }
    .tbl-card .card-header { background:#0b2b5c; color:#fff; border-radius:14px 14px 0 0; font-weight:600; }
    .badge-Pending  { background:#fef3c7; color:#92400e;  border-radius:20px; padding:3px 12px; font-weight:600; font-size:.82rem; }
    .badge-Approved { background:#d1fae5; color:#065f46;  border-radius:20px; padding:3px 12px; font-weight:600; font-size:.82rem; }
    .badge-Rejected { background:#fee2e2; color:#991b1b;  border-radius:20px; padding:3px 12px; font-weight:600; font-size:.82rem; }
    .filter-btn { border:1.5px solid #e2e8f0; border-radius:20px; padding:6px 18px; font-size:.88rem; font-weight:500; cursor:pointer; background:#fff; color:#374151; transition:.15s; margin-right:8px; }
    .filter-btn.active, .filter-btn:hover { background:#0b2b5c; color:#fff; border-color:#0b2b5c; }
    .doc-link { font-size:.82rem; }
    #bulkDeleteBar { display:none; background:#fff3cd; border:1.5px solid #ffc107; border-radius:10px; padding:10px 18px; margin-bottom:14px; align-items:center; gap:12px; }
    #bulkDeleteBar.show { display:flex; }
    #bulkDeleteBtn { background:#dc3545; color:#fff; border:none; border-radius:20px; padding:6px 20px; font-size:.88rem; font-weight:600; cursor:pointer; transition:.15s; }
    #bulkDeleteBtn:hover { background:#b02a37; }
    #selectCount { font-weight:600; color:#856404; }
</style>

<div class="container-fluid py-4">

    <?php if ($swal): ?>
    <script>
    window.addEventListener('load', function() {
        Swal.fire({
            icon: '<?= $swal['icon'] ?>',
            title: '<?= addslashes($swal['title']) ?>',
            text: '<?= addslashes($swal['text']) ?>'
        });
    });
    </script>
    <?php endif; ?>

    <div class="page-header">
        <h1><i class="fas fa-tasks me-2"></i> Promotion Requests</h1>
        <p class="mb-0 mt-1" style="opacity:.85;font-size:.95rem;">Review student-submitted promotion requests, verify their documents, and approve or reject them.</p>
    </div>

    <!-- Status filter -->
    <div class="mb-3">
        <a href="admn_promotion_requests.php" class="filter-btn <?= $status_filter === '' ? 'active' : '' ?>">All</a>
        <a href="admn_promotion_requests.php?status=Pending" class="filter-btn <?= $status_filter === 'Pending' ? 'active' : '' ?>">
            <i class="fas fa-clock me-1"></i> Pending
        </a>
        <a href="admn_promotion_requests.php?status=Approved" class="filter-btn <?= $status_filter === 'Approved' ? 'active' : '' ?>">
            <i class="fas fa-check-circle me-1"></i> Approved
        </a>
        <a href="admn_promotion_requests.php?status=Rejected" class="filter-btn <?= $status_filter === 'Rejected' ? 'active' : '' ?>">
            <i class="fas fa-times-circle me-1"></i> Rejected
        </a>
    </div>

    <!-- Bulk Delete Bar -->
    <div id="bulkDeleteBar">
        <span><i class="fas fa-check-square me-1"></i> <span id="selectCount">0</span> selected</span>
        <button id="bulkDeleteBtn" onclick="confirmBulkDelete()">
            <i class="fas fa-trash me-1"></i> Delete Selected
        </button>
        <button onclick="clearSelection()" style="background:none;border:none;color:#856404;font-size:.88rem;cursor:pointer;text-decoration:underline;">
            Clear selection
        </button>
    </div>

    <div class="tbl-card card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>
                <i class="fas fa-list me-2"></i> Requests
                <span class="badge bg-warning text-dark ms-2"><?= count(array_filter($requests, fn($r) => $r['status'] === 'Pending')) ?> Pending</span>
            </span>
            <?php if (!empty($requests)): ?>
            <div class="form-check mb-0" style="font-size:.88rem;">
                <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                <label class="form-check-label text-white" for="selectAll">Select All</label>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($requests)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3" style="color:#cbd5e1;"></i>
                    <p>No promotion requests found<?= $status_filter ? " with status <strong>{$status_filter}</strong>" : '' ?>.</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th style="width:40px;"><i class="fas fa-check-square text-muted"></i></th>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Submitted</th>
                            <th>Documents</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requests as $i => $r): ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-check form-check-input"
                                       value="<?= $r['id'] ?>" onchange="updateBulkBar()">
                            </td>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars(trim(($r['lname'] ?? '') . ', ' . ($r['fname'] ?? '') . ' ' . ($r['mi'] ?? ''))) ?></strong>
                                <br><small class="text-muted">Student #<?= $r['id_student'] ?></small>
                            </td>
                            <td>Grade <?= htmlspecialchars($r['from_grade']) ?></td>
                            <td>Grade <?= htmlspecialchars($r['to_grade']) ?></td>
                            <td style="font-size:.85rem;"><?= htmlspecialchars($r['submitted_at']) ?></td>
                            <td>
                                <?php
                                $docs = json_decode($r['documents'] ?? '[]', true);
                                if (!empty($docs)):
                                ?>
                                    <?php foreach ($docs as $doc): ?>
                                        <?php
                                        $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                        $icon = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? 'fa-image' : 'fa-file-alt';
                                        ?>
                                        <a href="<?= htmlspecialchars($doc) ?>" target="_blank" class="d-block doc-link text-primary">
                                            <i class="fas <?= $icon ?> me-1"></i><?= basename($doc) ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.85rem; max-width:180px;">
                                <?= htmlspecialchars($r['notes'] ?: '—') ?>
                                <?php if ($r['status'] === 'Rejected' && $r['reject_reason']): ?>
                                    <br><span class="text-danger"><i class="fas fa-times-circle"></i> <?= htmlspecialchars($r['reject_reason']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-<?= $r['status'] ?>"><?= htmlspecialchars($r['status']) ?></span>
                                <?php if ($r['reviewed_at']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($r['reviewed_at']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'Pending'): ?>
                                    <button class="btn btn-sm btn-success mb-1"
                                            onclick="approveRequest(<?= $r['id'] ?>, '<?= htmlspecialchars(trim(($r['lname']??'').', '.($r['fname']??''))) ?>', <?= $r['from_grade'] ?>, <?= $r['to_grade'] ?>)">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="rejectRequest(<?= $r['id'] ?>)">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:.82rem;">No action needed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Approve Form -->
<form method="POST" action="admn_promotion_requests.php" id="approveForm">
    <input type="hidden" name="approve_promotion_request" value="1">
    <input type="hidden" name="request_id" id="approve_request_id" value="">
    <input type="hidden" name="new_sy" id="approve_sy_hidden" value="">
</form>

<!-- Reject Form -->
<form method="POST" action="admn_promotion_requests.php" id="rejectForm">
    <input type="hidden" name="reject_promotion_request" value="1">
    <input type="hidden" name="request_id" id="reject_request_id" value="">
    <input type="hidden" name="reject_reason" id="reject_reason_hidden" value="">
</form>

<!-- Bulk Delete Form -->
<form method="POST" action="admn_promotion_requests.php" id="bulkDeleteForm">
    <input type="hidden" name="bulk_delete_promotion" value="1">
    <div id="bulkIdsContainer"></div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function getCheckedIds() {
    return [...document.querySelectorAll('.row-check:checked')].map(c => c.value);
}

function updateBulkBar() {
    const ids = getCheckedIds();
    const bar = document.getElementById('bulkDeleteBar');
    document.getElementById('selectCount').textContent = ids.length;
    ids.length > 0 ? bar.classList.add('show') : bar.classList.remove('show');

    // Sync select-all checkbox state
    const all = document.querySelectorAll('.row-check');
    const sa  = document.getElementById('selectAll');
    if (sa) sa.checked = all.length > 0 && ids.length === all.length;
}

function toggleSelectAll(cb) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
    updateBulkBar();
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    const sa = document.getElementById('selectAll');
    if (sa) sa.checked = false;
    updateBulkBar();
}

function confirmBulkDelete() {
    const ids = getCheckedIds();
    if (ids.length === 0) return;
    Swal.fire({
        title: 'Delete ' + ids.length + ' request(s)?',
        text: 'This action cannot be undone. The selected promotion requests will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulkIdsContainer');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'selected_ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('bulkDeleteForm').submit();
        }
    });
}

function approveRequest(id, name, from, to) {
    Swal.fire({
        title: 'Approve & Promote?',
        html: `<p>You are promoting <strong>${name}</strong> from <strong>Grade ${from}</strong> to <strong>Grade ${to}</strong>.</p>
               <label class="form-label fw-semibold mt-2">New School Year <span class="text-danger">*</span></label>
               <input id="sy_input" class="swal2-input" placeholder="e.g. 2026-2027" pattern="\\d{4}-\\d{4}">`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Approve & Promote',
        confirmButtonColor: '#16a34a',
        preConfirm: () => {
            const sy = document.getElementById('sy_input').value.trim();
            if (!/^\d{4}-\d{4}$/.test(sy)) {
                Swal.showValidationMessage('School year must be in YYYY-YYYY format.');
                return false;
            }
            return sy;
        }
    }).then(res => {
        if (res.isConfirmed) {
            document.getElementById('approve_request_id').value = id;
            document.getElementById('approve_sy_hidden').value  = res.value;
            document.getElementById('approveForm').submit();
        }
    });
}

function rejectRequest(id) {
    Swal.fire({
        title: 'Reject Request?',
        html: `<label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
               <textarea id="reason_input" class="swal2-textarea" placeholder="e.g. Missing report card, incomplete documents…"></textarea>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Reject',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
            const reason = document.getElementById('reason_input').value.trim();
            if (!reason) { Swal.showValidationMessage('Please provide a reason for rejection.'); return false; }
            return reason;
        }
    }).then(res => {
        if (res.isConfirmed) {
            document.getElementById('reject_request_id').value   = id;
            document.getElementById('reject_reason_hidden').value = res.value;
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>

<?php include('dashboard_sidebar_end.php'); ?>
