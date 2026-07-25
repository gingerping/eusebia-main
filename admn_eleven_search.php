<?php require 'classes/conn.php'; require_once 'classes/ai_review_ui.php'; ?>

<!-- ===== DOCUMENT VIEWER MODAL ===== -->
<div class="modal fade" id="docViewerModal" tabindex="-1" role="dialog" aria-labelledby="docViewerTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0b2b5c,#1f5a9e);color:white;">
                <h5 class="modal-title" id="docViewerTitle">
                    <i class="fas fa-file"></i>&nbsp;<span id="docViewerTitleText">Document Preview</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3" id="docViewerBody" style="min-height:300px;background:#f8f9fa;">
                <p class="text-muted pt-5">Loading...</p>
            </div>
            <div class="modal-footer">
                <a id="docViewerNewTab" href="#" target="_blank" class="btn btn-primary btn-sm" style="border-radius:20px;">
                    <i class="fas fa-external-link-alt"></i>&nbsp;Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:20px;">Close</button>
            </div>
        </div>
    </div>
</div>
<button id="docViewerRelay" data-toggle="modal" data-target="#docViewerModal" style="display:none;"></button>

<!-- ===== REJECT REASON MODAL ===== -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header" style="background:#c0392b;color:white;">
                <h5 class="modal-title"><i class="fas fa-times-circle mr-2"></i>Reject Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;"><span>&times;</span></button>
            </div>
            <form id="rejectForm" action="" method="POST">
                <input type="hidden" name="id_eleven" id="rejectIdEleven" value="">
                <div class="modal-body">
                    <p class="mb-1">Student: <strong id="rejectStudentName"></strong></p>
                    <p class="text-muted small mb-3">An email notification will be sent to the student.</p>
                    <div class="form-group">
                        <label for="reject_reason"><strong>Reason for Rejection</strong> <span class="text-muted">(optional)</span></label>
                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3"
                            placeholder="e.g. Incomplete documents, does not meet age requirement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="reject_eleven" class="btn btn-danger">
                        <i class="fas fa-times-circle mr-1"></i> Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#docViewerBody img { max-width:100%;max-height:68vh;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.15);object-fit:contain; }
#docViewerBody iframe { width:100%;height:68vh;border:none;border-radius:8px; }
.doc-preview-btn { display:inline-block;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle;cursor:pointer;transition:transform .15s,box-shadow .15s; }
.doc-preview-btn:hover { transform:scale(1.04);box-shadow:0 3px 10px rgba(42,111,156,.3); }
.doc-unsupported { padding:50px 20px;color:#6c757d; }
.doc-unsupported .big-icon { font-size:3rem;display:block;margin-bottom:12px;color:#adb5bd; }
.status-badge { font-size:12px;padding:4px 10px;border-radius:20px;font-weight:600;display:inline-block; }
.status-pending  { background:#fff3cd;color:#856404;border:1px solid #ffc107; }
.status-approved { background:#d4edda;color:#155724;border:1px solid #28a745; }
.status-rejected { background:#f8d7da;color:#721c24;border:1px solid #dc3545; }
</style>

<?php

function renderDocs_eleven($docsJson) {
    $docs = json_decode($docsJson ?? '[]', true);
    if (is_array($docs) && array_key_exists('admin_marked', $docs)) {
        if ($docs['admin_marked'] === 'Incomplete') {
            echo '<span class="badge badge-warning" title="' . htmlspecialchars($docs['note'] ?? '') . '"><i class="fas fa-exclamation-triangle mr-1"></i>Incomplete</span>';
            if (!empty($docs['note'])) echo '<br><span class="text-muted small">' . htmlspecialchars($docs['note']) . '</span>';
        } else {
            echo '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Complete (added by admin)</span>';
        }
        return;
    }
    if (empty($docs)) { echo '<span class="text-muted small">No documents</span>'; return; }
    foreach ($docs as $docPath) {
        $fileName = basename($docPath);
        $ext   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
        $isPdf = $ext === 'pdf';
        $icon  = $isImg ? 'fa-image' : ($isPdf ? 'fa-file-pdf' : 'fa-file-word');
        $type  = $isImg ? 'image' : ($isPdf ? 'pdf' : 'doc');
        echo '<button type="button" class="btn btn-outline-primary btn-sm mb-1 doc-preview-btn"
                onclick="openDocViewer(\''.addslashes(htmlspecialchars($docPath)).'\',\''.addslashes(htmlspecialchars($fileName)).'\',\''.($type).'\' )"
                title="'.htmlspecialchars($fileName).'">
                <i class="fas '.$icon.'"></i> '.htmlspecialchars($fileName).'
              </button><br>';
    }
}

function renderStatus_eleven($status) {
    $status = $status ?: 'Pending';
    $cls = ['Approved'=>'status-approved','Rejected'=>'status-rejected','Pending'=>'status-pending'];
    $ico = ['Approved'=>'fa-check-circle','Rejected'=>'fa-times-circle','Pending'=>'fa-clock'];
    $c   = $cls[$status] ?? 'status-pending';
    $i   = $ico[$status] ?? 'fa-clock';
    echo '<span class="status-badge '.$c.'"><i class="fas '.$i.' mr-1"></i>'.htmlspecialchars($status).'</span>';
}

function renderActions_eleven($id_col_val, $id_student, $status, $fname, $lname, $mi, $prefix = '') {
    $fullName = htmlspecialchars($lname.', '.$fname.' '.$mi);
    $modalId  = 'viewModal'.$prefix.$id_student;
    $ddId     = 'actionsDd'.$prefix.$id_col_val;
    ?>
    <div class="dropdown">
        <button class="btn btn-outline-primary btn-sm dropdown-toggle actions-dropdown-toggle" type="button"
            id="<?= $ddId ?>" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i> Actions
        </button>
        <div class="dropdown-menu dropdown-menu-right actions-dropdown-menu" aria-labelledby="<?= $ddId ?>" data-boundary="window">
            <div class="actions-dropdown-header">Actions</div>
            <div class="actions-dropdown-body">

            <a class="dropdown-item item-view" href="#" data-toggle="modal" data-target="#<?= $modalId ?>">
                <span class="action-icon-badge"><i class="fa fa-eye"></i></span>View
            </a>

            <form action="" method="post">
                <input type="hidden" name="id_eleven" value="<?= $id_col_val ?>">
                <button class="dropdown-item item-archive" type="submit" name="delete_eleven">
                    <span class="action-icon-badge"><i class="fas fa-archive"></i></span>Archive
                </button>
            </form>

            <?php if ($status !== 'Approved' && $status !== 'Rejected'): ?>
                <div class="dropdown-divider"></div>
                <form action="" method="post" onsubmit="return confirmApprove_eleven(this);">
                    <input type="hidden" name="id_eleven" value="<?= $id_col_val ?>">
                    <input type="hidden" name="approve_eleven" value="1">
                    <button class="dropdown-item item-approve" type="submit">
                        <span class="action-icon-badge"><i class="fas fa-check"></i></span>Approve
                    </button>
                </form>
                <button class="dropdown-item item-reject" type="button"
                    onclick="openRejectModal_eleven(<?= $id_col_val ?>, '<?= addslashes($fullName) ?>')">
                    <span class="action-icon-badge"><i class="fas fa-times"></i></span>Reject
                </button>
            <?php elseif ($status === 'Rejected'): ?>
                <div class="dropdown-divider"></div>
                <form action="" method="post" onsubmit="return confirmApprove_eleven(this);">
                    <input type="hidden" name="id_eleven" value="<?= $id_col_val ?>">
                    <input type="hidden" name="approve_eleven" value="1">
                    <button class="dropdown-item item-approve" type="submit">
                        <span class="action-icon-badge"><i class="fas fa-check"></i></span>Approve
                    </button>
                </form>
            <?php endif; ?>

            </div>
        </div>
    </div>
<?php
}

?>


<!-- ===== ENROLLEES TABLE (card, matches staff view style) ===== -->
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex align-items-center justify-content-between"
         style="background:linear-gradient(135deg,#0b2b5c,#1f5a9e);">
        <span class="text-white font-weight-bold">
            <i class="fas fa-table mr-1"></i> Grade 11 Enrollees
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm mb-0" id="studentsTable" style="font-size:.84rem;">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>LRN</th><th>Course</th><th>Full Name</th><th>Birthday</th><th>Age</th>
                        <th>Contact</th><th>Email</th><th>Documents</th><th>AI Review</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">


    <?php if (is_array($view)): foreach ($view as $row):
        $rStatus = $row['enrollment_status'] ?? 'Pending';
    ?>
        <tr>
            <td><?= htmlspecialchars($row['lrn']) ?></td>
            <td><?= htmlspecialchars($row['course']) ?></td>
            <td><?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['bdate']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td style="min-width:145px;"><?php renderDocs_eleven($row['documents'] ?? ''); ?></td>
            <td style="min-width:150px;"><?php render_ai_review_cell($row['ai_analysis'] ?? null, 'eleven', $row['id_eleven']); ?></td>
            <td><?php renderStatus_eleven($rStatus); ?></td>
            <td style="min-width:220px;">
                <?php renderActions_eleven($row['id_eleven'], $row['id_student'], $rStatus,
                    $row['fname'], $row['lname'], $row['mi']); ?>
            </td>
        </tr>

        <!-- View Modal (default) -->
        <div class="modal fade" id="viewModal<?= $row['id_student'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Student Information</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-left">
                        <p><strong>School Year:</strong> <?= htmlspecialchars($row['sy']) ?></p>
                        <p><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></p>
                        <p><strong>LRN:</strong> <?= htmlspecialchars($row['lrn']) ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                        <h5><strong>Personal Information</strong></h5>
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></p>
                        <p><strong>Birthday:</strong> <?= htmlspecialchars($row['bdate']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></p>
                        <p><strong>Contact Number:</strong> <?= htmlspecialchars($row['contact']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Current Address:</strong> <?= htmlspecialchars($row['current_address']) ?></p>
                        <p><strong>Permanent Address:</strong> <?= htmlspecialchars($row['perm_address']) ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                        <h5><strong>Father's Information</strong></h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($row['flname']) ?>, <?= htmlspecialchars($row['ffname']) ?> <?= htmlspecialchars($row['fmi']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_f']) ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                        <h5><strong>Mother's Information</strong></h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($row['mlname']) ?>, <?= htmlspecialchars($row['mfname']) ?> <?= htmlspecialchars($row['mmi']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_m']) ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                        <h5><strong>For Returning Learner</strong></h5>
                        <p><strong>Last Grade Level Completed:</strong> <?= htmlspecialchars($row['lglc']) ?></p>
                        <p><strong>Last School Attended:</strong> <?= htmlspecialchars($row['lsa']) ?></p>
                        <p><strong>Last School Year Completed:</strong> <?= htmlspecialchars($row['lysc']) ?></p>
                        <p><strong>School ID:</strong> <?= htmlspecialchars($row['school_id']) ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                         <h5><strong>Socioeconomic Information</strong></h5>
                        <p><strong>IP Member:</strong> <?= htmlspecialchars($row['is_ip'] ?? 'No') ?><?= (!empty($row['ip_group'])) ? ' — ' . htmlspecialchars($row['ip_group']) : '' ?></p>
                        <p><strong>4Ps Beneficiary:</strong> <?= htmlspecialchars($row['is_4ps'] ?? 'No') ?><?= (!empty($row['fourps_id'])) ? ' — ID: ' . htmlspecialchars($row['fourps_id']) : '' ?></p>
                        <hr style="border:2px solid black;opacity:1;">
                        <p><strong>Status:</strong> <?php renderStatus_eleven($rStatus); ?></p>
                        <?php if ($rStatus === 'Rejected' && !empty($row['reject_reason'])): ?>
                        <p><strong>Rejection Reason:</strong> <?= htmlspecialchars($row['reject_reason']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; else: ?>
                <tr><td colspan="11" class="text-center text-muted py-4">No students found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function(){
    $('#studentsTable').DataTable({ pageLength: 20, order: [[1,'asc']] });
});
</script>


<script>
function openDocViewer(path, name, type) {
    document.getElementById('docViewerTitleText').textContent = name;
    document.getElementById('docViewerNewTab').setAttribute('href', path);
    var body = document.getElementById('docViewerBody');
    if (type === 'image') {
        body.innerHTML = '<img src="' + path + '" alt="' + name + '">';
    } else if (type === 'pdf') {
        body.innerHTML = '<iframe src="' + path + '" title="' + name + '"></iframe>';
    } else {
        body.innerHTML = '<div class="doc-unsupported"><i class="fas fa-file-word big-icon"></i><strong>' + name + '</strong><p class="mt-2 text-muted">This file type cannot be previewed here.<br>Use <strong>Open in New Tab</strong> to download and view it.</p></div>';
    }
    document.getElementById('docViewerRelay').click();
}
document.getElementById('docViewerModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('docViewerBody').innerHTML = '<p class="text-muted pt-5">Loading...</p>';
    document.getElementById('docViewerTitleText').textContent = 'Document Preview';
    document.getElementById('docViewerNewTab').setAttribute('href', '#');
});

function confirmApprove_eleven(form) {
    var row  = form.closest('tr');
    var name = row ? row.cells[1].innerText.trim() : 'this student';
    Swal.fire({
        title: 'Approve Enrollment?',
        html: 'Are you sure you want to approve <strong>' + name + "</strong>'s enrollment?<br><br>An email notification will be sent to the student.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0b2b5c',
        cancelButtonColor:  '#d33',
        confirmButtonText:  'Yes, Approve!',
        cancelButtonText:   'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) { form.submit(); }
    });
    return false;
}

function openRejectModal_eleven(id_col_val, studentName) {
    document.getElementById('rejectIdEleven').value = id_col_val;
    document.getElementById('rejectStudentName').textContent = studentName;
    document.getElementById('reject_reason').value = '';
    $('#rejectModal').modal('show');
}
</script>