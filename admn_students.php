<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
    require('classes/student.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    require_once('classes/staff.class.php');
    $staffeusebia->promote_student_to_staff();

    // Fetch all registered student accounts
    $connection = $eusebia->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_student ORDER BY lname ASC, fname ASC");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
.res-header {
    background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
    color: #fff;
    padding: 18px 24px;
    border-radius: 8px;
    margin-bottom: 24px;
}
.res-header h4 { margin-bottom: 2px; }
.total-badge {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    font-weight: 700;
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 14px;
}
.table thead th {
    background: #0b2b5c;
    color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    vertical-align: middle;
}
.table tbody tr:hover { background-color: #eaf0fb; }
.table tbody tr.row-selected { background-color: #d6e8f7 !important; }
.search-wrap input {
    border: 1px solid #c5d5e8;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 14px;
    width: 260px;
}
.search-wrap input:focus {
    outline: none;
    border-color: #0b2b5c;
    box-shadow: 0 0 0 2px rgba(11,43,92,0.1);
}
#entriesCount { font-size: 13px; color: #6c757d; }
#btnDeleteSelected {
    display: none;
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
#btnDeleteSelected:hover { background: #a93226; }
#selectedCount {
    font-size: 13px;
    font-weight: 600;
    color: #c0392b;
    margin-right: 8px;
    display: none;
}
.cb-row { cursor: pointer; width: 18px; height: 18px; accent-color: #0b2b5c; }
.th-check { width: 42px; text-align: center; }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Header -->
    <div class="res-header d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h4 class="mb-1"><i class="fas fa-users mr-2"></i>Registered Students</h4>
            <small class="opacity-75">All accounts registered in the portal.</small>
        </div>
        <span class="total-badge mt-2 mt-md-0">
            <i class="fas fa-user-check mr-1"></i>
            Total: <?= number_format(count($students)) ?>
        </span>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 font-weight-bold" style="color:#0b2b5c;">
                <i class="fas fa-table mr-1"></i> Student List
            </h6>
            <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0" style="gap:10px;">
                <span id="selectedCount">0 selected</span>
                <button id="btnDeleteSelected" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Selected
                </button>
                <div class="search-wrap">
                    <input type="text" id="studentSearch" placeholder="&#128269; Search name, email...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="studentTable">
                    <thead>
                        <tr>
                            <th class="th-check">
                                <input type="checkbox" id="selectAll" class="cb-row" title="Select All">
                            </th>
                            <th style="width:45px;">#</th>
                            <th>Full Name</th>
                            <th>Birthdate</th>
                            <th>Email / Phone</th>
                            <th>Registered By</th>
                            <th style="width:120px;">Promote</th>
                        </tr>
                    </thead>
                    <tbody id="studentTbody">
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No registered students found.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($students as $i => $r):
                            $mi       = !empty($r['mi']) ? ' ' . strtoupper($r['mi']) . '.' : '';
                            $fullname = strtoupper($r['lname']) . ', ' . ucwords(strtolower($r['fname'])) . $mi;
                            $contact  = !empty($r['email']) ? $r['email'] : ($r['phone_number'] ?? '—');
                            $bdate    = !empty($r['bdate']) ? date('M d, Y', strtotime($r['bdate'])) : '—';
                        ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="cb-row cb-student" value="<?= $r['id_student'] ?>">
                            </td>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td class="font-weight-bold" style="color:#0b2b5c;"><?= htmlspecialchars($fullname) ?></td>
                            <td><?= $bdate ?></td>
                            <td><?= htmlspecialchars($contact) ?></td>
                            <td><?= htmlspecialchars($r['addedby'] ?? '—') ?></td>
                            <td class="text-center">
                                <?php
                                // Check if already a teacher
                                $chk = $connection->prepare("SELECT id_user FROM tbl_user WHERE email=? OR (email IS NULL AND contact=?)");
                                $chk->execute([$r['email'], $r['contact']]);
                                $already = $chk->rowCount() > 0;
                                ?>
                                <?php if ($already): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Teacher</span>
                                <?php else: ?>
                                <button class="btn btn-outline-success btn-sm py-0 px-2 promote-btn"
                                    data-id="<?= $r['id_student'] ?>"
                                    data-name="<?= htmlspecialchars(strtoupper($r['lname']).', '.ucwords(strtolower($r['fname']))) ?>"
                                    data-email="<?= htmlspecialchars($r['email'] ?? '') ?>"
                                    data-contact="<?= htmlspecialchars($r['contact']) ?>"
                                    data-toggle="modal" data-target="#promoteModal">
                                    <i class="fas fa-chalkboard-teacher"></i> Promote
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted d-flex justify-content-between align-items-center py-2">
            <small id="entriesCount">Showing <strong id="visibleCount"><?= count($students) ?></strong> of <strong><?= count($students) ?></strong> entries</small>
            <small>Sorted by last name A–Z</small>
        </div>
    </div>

</div><!-- /.container-fluid -->

<!-- ======== PROMOTE MODAL ======== -->
<div class="modal fade" id="promoteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#155724,#1e7e34);">
                <h5 class="modal-title"><i class="fas fa-chalkboard-teacher mr-2"></i>Promote to Teacher / Staff</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="alert alert-success py-2 mb-3">
                        <i class="fas fa-user mr-1"></i>
                        Promoting: <strong id="promote_name_display"></strong>
                    </div>
                    <input type="hidden" name="id_student" id="promote_id_student">

                    <div class="form-group">
                        <label class="font-weight-bold small">Position <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="position" required>
                            <option value="">— Select Position —</option>
                            <option value="Teacher I">Teacher I</option>
                            <option value="Teacher II">Teacher II</option>
                            <option value="Teacher III">Teacher III</option>
                            <option value="Master Teacher I">Master Teacher I</option>
                            <option value="Master Teacher II">Master Teacher II</option>
                            <option value="Head Teacher">Head Teacher</option>
                            <option value="Registrar">Registrar</option>
                            <option value="Guidance Counselor">Guidance Counselor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Subject(s) Handled</label>
                        <input type="text" class="form-control form-control-sm" name="subject_handled" placeholder="e.g. Math, Science, English">
                        <small class="text-muted">Separate multiple subjects with commas</small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Assign as Adviser of Grade/Section</label>
                        <select class="form-control form-control-sm" name="adviser_grade">
                            <option value="">— Not an Adviser —</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11 - STEM">Grade 11 - STEM</option>
                            <option value="Grade 11 - ABM">Grade 11 - ABM</option>
                            <option value="Grade 11 - GAS">Grade 11 - GAS</option>
                            <option value="Grade 11 - TVL-ICT">Grade 11 - TVL-ICT</option>
                            <option value="Grade 11 - TVL-HE">Grade 11 - TVL-HE</option>
                            <option value="Grade 12 - STEM">Grade 12 - STEM</option>
                            <option value="Grade 12 - ABM">Grade 12 - ABM</option>
                            <option value="Grade 12 - GAS">Grade 12 - GAS</option>
                            <option value="Grade 12 - TVL-ICT">Grade 12 - TVL-ICT</option>
                            <option value="Grade 12 - TVL-HE">Grade 12 - TVL-HE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="promote_student" class="btn btn-success btn-sm">
                        <i class="fas fa-user-check mr-1"></i> Confirm Promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// Populate promote modal
$(document).on('click', '.promote-btn', function() {
    const b = $(this);
    $('#promote_id_student').val(b.data('id'));
    $('#promote_name_display').text(b.data('name'));
});
</script>

<script>
// ── Select All ──────────────────────────────────────────────────────────────
document.getElementById('selectAll').addEventListener('change', function () {
    const visibleCheckboxes = [...document.querySelectorAll('#studentTbody tr')]
        .filter(r => r.style.display !== 'none')
        .map(r => r.querySelector('.cb-student'))
        .filter(Boolean);

    visibleCheckboxes.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('row-selected', this.checked);
    });
    updateDeleteBar();
});

// ── Per-row checkbox ─────────────────────────────────────────────────────────
document.getElementById('studentTbody').addEventListener('change', function (e) {
    if (e.target.classList.contains('cb-student')) {
        e.target.closest('tr').classList.toggle('row-selected', e.target.checked);
        syncSelectAll();
        updateDeleteBar();
    }
});

function syncSelectAll() {
    const all     = [...document.querySelectorAll('.cb-student')].filter(cb => cb.closest('tr').style.display !== 'none');
    const checked = all.filter(cb => cb.checked);
    const sa      = document.getElementById('selectAll');
    sa.checked       = all.length > 0 && checked.length === all.length;
    sa.indeterminate = checked.length > 0 && checked.length < all.length;
}

function updateDeleteBar() {
    const count = document.querySelectorAll('.cb-student:checked').length;
    const btn   = document.getElementById('btnDeleteSelected');
    const lbl   = document.getElementById('selectedCount');
    if (count > 0) {
        btn.style.display = 'inline-block';
        lbl.style.display = 'inline';
        lbl.textContent   = count + ' selected';
    } else {
        btn.style.display = 'none';
        lbl.style.display = 'none';
    }
}

// ── Bulk delete ──────────────────────────────────────────────────────────────
function confirmBulkDelete() {
    const ids = [...document.querySelectorAll('.cb-student:checked')].map(cb => cb.value);
    if (ids.length === 0) return;

    Swal.fire({
        title: 'Delete ' + ids.length + ' account' + (ids.length > 1 ? 's' : '') + '?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;

        fetch('delete_bulk_students.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: ids })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.deleted + ' account(s) removed.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Request failed. Please try again.', 'error'));
    });
}

// ── Live search ──────────────────────────────────────────────────────────────
document.getElementById('studentSearch').addEventListener('keyup', function () {
    const query = this.value.toLowerCase().trim();
    const rows  = document.querySelectorAll('#studentTbody tr');
    let visible = 0;

    rows.forEach(row => {
        const match = row.innerText.toLowerCase().includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('visibleCount').textContent = visible;
    syncSelectAll();
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>