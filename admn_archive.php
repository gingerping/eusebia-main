<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
    require('classes/main.class.php');
    require('classes/resident.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    // Handle restore actions
    $eusebia->restore_seven();
    $eusebia->restore_eight();
    $eusebia->restore_nine();
    $eusebia->restore_ten();
    $eusebia->restore_eleven();
    $eusebia->restore_twelve();

    // Handle permanent delete
    if (isset($_POST['permanent_delete'])) {
        $grade_table = $_POST['grade_table'];
        $record_id   = $_POST['record_id'];
        $allowed_tables = ['seven','eight','nine','ten','eleven','twelve'];
        if (in_array($grade_table, $allowed_tables)) {
            $connection = $eusebia->openConn();
            $id_col = 'id_' . $grade_table;
            $stmt = $connection->prepare("DELETE FROM tbl_{$grade_table} WHERE {$id_col} = ? AND is_archived = 1");
            $stmt->execute([$record_id]);
        }
        header("Location: admn_archive.php");
        exit();
    }

    // Collect all archived records
    $archived_seven   = $eusebia->view_archived_seven()   ?: [];
    $archived_eight   = $eusebia->view_archived_eight()   ?: [];
    $archived_nine    = $eusebia->view_archived_nine()    ?: [];
    $archived_ten     = $eusebia->view_archived_ten()     ?: [];
    $archived_eleven  = $eusebia->view_archived_eleven()  ?: [];
    $archived_twelve  = $eusebia->view_archived_twelve()  ?: [];

    $all_archived = array_merge(
        $archived_seven, $archived_eight, $archived_nine,
        $archived_ten, $archived_eleven, $archived_twelve
    );

    // Sort by archived_at descending
    usort($all_archived, function($a, $b) {
        return strtotime($b['archived_at'] ?? '0') - strtotime($a['archived_at'] ?? '0');
    });

    // Filter by grade if requested
    $filter_grade = $_GET['grade'] ?? 'all';

    // Search keyword
    $keyword = strtolower(trim($_GET['keyword'] ?? ''));
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .archive-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 20px;
    }
    .table th { font-size: 13px; }
    .table td { font-size: 13px; vertical-align: middle; }
    .grade-filter-btn {
        border-radius: 20px;
        margin: 0 3px 6px 0;
        font-size: 13px;
    }
    .archive-header {
        background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
        color: white;
        padding: 18px 24px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    .empty-archive {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }
    .empty-archive i { font-size: 60px; margin-bottom: 16px; color: #ccc; }
    .delete-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 20, 40, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.delete-modal-overlay.show {
    display: flex;
}
.delete-modal {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    max-width: 380px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: popIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    text-align: center;
}
@keyframes popIn {
    from { transform: scale(0.8); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.delete-modal-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #fff0f0, #ffe0e0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.2rem;
    border: 3px solid #f5c6c6;
}
.delete-modal-icon i {
    font-size: 1.8rem;
    color: #e74c3c;
}
.delete-modal h5 {
    font-family: 'Segoe UI', sans-serif;
    font-weight: 800;
    font-size: 1.15rem;
    color: #1a1a2e;
    margin-bottom: 0.5rem;
}
.delete-modal p {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}
.delete-modal-warning {
    background: #fff8e1;
    border: 1px solid #ffe082;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.78rem;
    color: #f39c12;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.delete-modal-actions {
    display: flex;
    gap: 10px;
}
.btn-cancel-modal {
    flex: 1;
    padding: 10px;
    border-radius: 12px;
    border: 1.5px solid #e0e0e0;
    background: #f8f9fa;
    color: #555;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-cancel-modal:hover {
    background: #e9ecef;
    border-color: #ccc;
}
.btn-confirm-delete {
    flex: 1;
    padding: 10px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: white;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(231,76,60,0.4);
    transition: all 0.2s;
}
.btn-confirm-delete:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(231,76,60,0.5);
}
.restore-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 20, 40, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.restore-modal-overlay.show {
    display: flex;
}
.restore-modal {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    max-width: 380px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: popIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    text-align: center;
}
.restore-modal-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #eafaf1, #d5f5e3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.2rem;
    border: 3px solid #a9dfbf;
}
.restore-modal-icon i {
    font-size: 1.8rem;
    color: #27ae60;
}
.restore-modal h5 {
    font-family: 'Segoe UI', sans-serif;
    font-weight: 800;
    font-size: 1.15rem;
    color: #1a1a2e;
    margin-bottom: 0.5rem;
}
.restore-modal p {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}
.restore-modal-info {
    background: #eafaf1;
    border: 1px solid #a9dfbf;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.78rem;
    color: #27ae60;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.restore-modal-actions {
    display: flex;
    gap: 10px;
}
.btn-cancel-restore {
    flex: 1;
    padding: 10px;
    border-radius: 12px;
    border: 1.5px solid #e0e0e0;
    background: #f8f9fa;
    color: #555;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-cancel-restore:hover {
    background: #e9ecef;
    border-color: #ccc;
}
.btn-confirm-restore {
    flex: 1;
    padding: 10px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #1e8449, #27ae60);
    color: white;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(39,174,96,0.4);
    transition: all 0.2s;
}
.btn-confirm-restore:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(39,174,96,0.5);
}
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="archive-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1"><i class="fas fa-archive mr-2"></i>Archive</h4>
            <small class="opacity-75">Recently archived enrollment records. Restore or permanently delete them.</small>
        </div>
        <span class="badge badge-light text-dark px-3 py-2" style="font-size:14px;">
            <?= count($all_archived) ?> Record<?= count($all_archived) != 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- Filters Row -->
    <div class="card shadow mb-4">
        <div class="card-body pb-2">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2">
                    <strong class="mr-2">Filter by Grade:</strong>
                    <?php
                    $grades = ['all' => 'All Grades', 'seven' => 'Grade 7', 'eight' => 'Grade 8',
                               'nine' => 'Grade 9', 'ten' => 'Grade 10', 'eleven' => 'Grade 11', 'twelve' => 'Grade 12'];
                    foreach ($grades as $val => $label):
                        $active = ($filter_grade === $val) ? 'btn-primary' : 'btn-outline-primary';
                    ?>
                        <a href="admn_archive.php?grade=<?= $val ?>&keyword=<?= urlencode($keyword) ?>"
                           class="btn btn-sm <?= $active ?> grade-filter-btn"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6 mb-2">
                    <form method="GET" action="admn_archive.php" class="d-flex">
                        <input type="hidden" name="grade" value="<?= htmlspecialchars($filter_grade) ?>">
                        <input type="search" name="keyword" class="form-control form-control-sm mr-2"
                               placeholder="Search by name or LRN..." value="<?= htmlspecialchars($keyword) ?>">
                        <button class="btn btn-sm btn-success" type="submit">Search</button>
                        <?php if ($keyword): ?>
                            <a href="admn_archive.php?grade=<?= $filter_grade ?>" class="btn btn-sm btn-secondary ml-1">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i> Archived Records
            </h6>
        </div>
        <div class="card-body p-0">
            <?php
            // Apply filters
            $display_records = array_filter($all_archived, function($row) use ($filter_grade, $keyword) {
                $grade_match = ($filter_grade === 'all') || ($row['grade_table'] === $filter_grade);
                if (!$grade_match) return false;
                if ($keyword) {
                    $searchable = strtolower(
                        ($row['lname'] ?? '') . ' ' . ($row['fname'] ?? '') . ' ' .
                        ($row['mi'] ?? '') . ' ' . ($row['lrn'] ?? '')
                    );
                    return strpos($searchable, $keyword) !== false;
                }
                return true;
            });
            ?>

            <?php if (empty($display_records)): ?>
                <div class="empty-archive">
                    <i class="fas fa-inbox d-block"></i>
                    <h5>No archived records found</h5>
                    <p>Records you archive from the grade enrollment pages will appear here.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Grade</th>
                                <th>LRN</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>M.I.</th>
                                <th>School Year</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Archived At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $ctr = 1; foreach ($display_records as $row): ?>
                            <tr>
                                <td><?= $ctr++ ?></td>
                                <td>
                                    <span class="badge badge-primary archive-badge">
                                        <?= htmlspecialchars($row['grade_label']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['lrn'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['lname'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['fname'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['mi'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['sy'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['sex'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['age'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($row['archived_at'])): ?>
                                        <small class="text-muted">
                                            <?= date('M d, Y g:i A', strtotime($row['archived_at'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">—</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php $gt = $row['grade_table']; $rid = $row['record_id']; ?>
                                    <!-- Restore -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_<?= $gt ?>" value="<?= $rid ?>">
                                        <button type="button"
        class="btn btn-sm d-inline-flex align-items-center gap-1"
        title="Restore Record"
        style="border: 1.5px solid #27ae60;
               background: rgba(39,174,96,0.08);
               color: #27ae60;
               border-radius: 8px;
               padding: 6px 12px;
               font-weight: 700;
               font-size: 0.75rem;
               transition: all 0.2s;"
        onmouseover="this.style.background='#27ae60'; this.style.color='white'"
        onmouseout="this.style.background='rgba(39,174,96,0.08)'; this.style.color='#27ae60'"
        onclick="openRestoreModal(this, '<?= strtoupper(substr($gt,0,1)).substr($gt,1) ?>')">
    <i class="fas fa-undo" style="font-size:0.72rem;"></i>
    <span>Restore</span>
</button>
                                    </form>
                                    <!-- Permanent Delete -->
                                    <form method="POST" style="display:inline;" class="ml-1">
                                        <input type="hidden" name="grade_table" value="<?= $gt ?>">
                                        <input type="hidden" name="record_id" value="<?= $rid ?>">
                                        <button type="button"
        class="btn btn-sm"
        title="Permanently Delete"
        style="width: 32px; height: 32px; padding: 0; border-radius: 8px;
               border: 1.5px solid #e74c3c; background: rgba(231,76,60,0.08);
               color: #e74c3c; display: inline-flex; align-items: center;
               justify-content: center; transition: all 0.2s;"
        onmouseover="this.style.background='#e74c3c'; this.style.color='white'"
        onmouseout="this.style.background='rgba(231,76,60,0.08)'; this.style.color='#e74c3c'"
        onclick="openDeleteModal(this)">
    <i class="fas fa-trash-alt" style="font-size:0.72rem;"></i>
</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($display_records)): ?>
        <div class="card-footer text-muted small">
            Showing <?= count($display_records) ?> archived record<?= count($display_records) != 1 ? 's' : '' ?>.
            Restoring a record moves it back to its original grade enrollment list.
        </div>
        <?php endif; ?>
    </div>

</div>
<div class="delete-modal-overlay" id="deleteModalOverlay">
    <div class="delete-modal">
        <div class="delete-modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h5>Delete Permanently?</h5>
        <p>You are about to permanently remove this record from the system. This action is irreversible.</p>
        <div class="delete-modal-warning">
            <i class="fas fa-exclamation-triangle"></i>
            This cannot be undone!
        </div>
        <div class="delete-modal-actions">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()">
                Cancel
            </button>
            <!-- This submits the actual form -->
            <button class="btn-confirm-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash-alt"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>
<div class="restore-modal-overlay" id="restoreModalOverlay">
    <div class="restore-modal">
        <div class="restore-modal-icon">
            <i class="fas fa-undo"></i>
        </div>
        <h5>Restore Record?</h5>
        <p>You are about to restore this record back to <strong id="restoreGradeLabel"></strong>. The record will be active again.</p>
        <div class="restore-modal-info">
            <i class="fas fa-check-circle"></i>
            This record will be fully recovered!
        </div>
        <div class="restore-modal-actions">
            <button class="btn-cancel-restore" onclick="closeRestoreModal()">
                Cancel
            </button>
            <button class="btn-confirm-restore" id="confirmRestoreBtn">
                <i class="fas fa-undo"></i> Yes, Restore
            </button>
        </div>
    </div>
</div>
<script>
let restoreTargetForm = null;
let restoreInputName  = null;

function openRestoreModal(btn, gradeLabel) {
    restoreTargetForm = btn.closest('form');
    restoreInputName  = 'restore_<?= $gt ?>';
    document.getElementById('restoreGradeLabel').textContent = 'Grade ' + gradeLabel;
    document.getElementById('restoreModalOverlay').classList.add('show');
}

function closeRestoreModal() {
    document.getElementById('restoreModalOverlay').classList.remove('show');
    restoreTargetForm = null;
    restoreInputName  = null;
}

document.getElementById('confirmRestoreBtn').addEventListener('click', function () {
    if (restoreTargetForm && restoreInputName) {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = restoreInputName;
        input.value = '1';
        restoreTargetForm.appendChild(input);
        restoreTargetForm.submit();
    }
    closeRestoreModal();
});

// Close on overlay click
document.getElementById('restoreModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeRestoreModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRestoreModal();
});
</script>
<script>
let targetForm = null;

function openDeleteModal(btn) {
    // Find the closest form to submit on confirm
    targetForm = btn.closest('form');
    document.getElementById('deleteModalOverlay').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModalOverlay').classList.remove('show');
    targetForm = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (targetForm) {
        // Add hidden input to trigger permanent_delete
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'permanent_delete';
        input.value = '1';
        targetForm.appendChild(input);
        targetForm.submit();
    }
    closeDeleteModal();
});

// Close on overlay click
document.getElementById('deleteModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
<!-- /.container-fluid -->

<?php include('dashboard_sidebar_end.php'); ?>
