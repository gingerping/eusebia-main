<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
    require('classes/staff.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    $staffeusebia->update_staff();
    $staffeusebia->create_staff();
    $staffeusebia->delete_staff();

    $view       = $staffeusebia->view_staff();
    $staffcount = $staffeusebia->count_staff();
?>
<?php include('dashboard_sidebar_start.php'); ?>

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Teacher &amp; Adviser Management
            </h4>
            <small class="text-muted">Manage school teachers and their section/subject assignments</small>
        </div>

    </div>
    <hr>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Teachers</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $staffcount ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Male</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $staffeusebia->count_mstaff() ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-male fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Female</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $staffeusebia->count_fstaff() ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-female fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white font-weight-bold py-2">
            <i class="fas fa-table mr-1"></i> Teacher Records
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm mb-0" id="teacherTable">
                    <thead class="thead-dark text-center" style="font-size:0.8rem;">
                        <tr>
                            <th>#</th><th>Name</th><th>Email</th><th>Contact</th>
                            <th>Position</th><th>Subject Handled</th><th>Adviser of</th>
                            <th>Sex</th><th>Added By</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size:0.85rem;">
                        <?php if (is_array($view) && count($view) > 0): $row = 1; foreach ($view as $t): ?>
                        <tr>
                            <td><?= $row++ ?></td>
                            <td class="text-left"><strong><?= htmlspecialchars($t['lname'].', '.$t['fname'].' '.$t['mi']) ?></strong></td>
                            <td><?= htmlspecialchars($t['email']) ?></td>
                            <td><?= htmlspecialchars($t['contact']) ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($t['position']) ?></span></td>
                            <td><?= !empty($t['subject_handled']) ? htmlspecialchars($t['subject_handled']) : '<span class="text-muted">—</span>' ?></td>
                            <td>
                                <?php if (!empty($t['adviser_grade'])): ?>
                                    <span class="badge badge-success"><?= htmlspecialchars($t['adviser_grade']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($t['sex']) ?></td>
                            <td><small><?= htmlspecialchars($t['addedby'] ?? '—') ?></small></td>
                            <td>
                                <button class="btn btn-warning btn-sm py-0 px-2 edit-btn"
                                    data-id="<?= $t['id_user'] ?>"
                                    data-lname="<?= htmlspecialchars($t['lname']) ?>"
                                    data-fname="<?= htmlspecialchars($t['fname']) ?>"
                                    data-mi="<?= htmlspecialchars($t['mi']) ?>"
                                    data-age="<?= $t['age'] ?>"
                                    data-sex="<?= $t['sex'] ?>"
                                    data-email="<?= htmlspecialchars($t['email']) ?>"
                                    data-contact="<?= htmlspecialchars($t['contact']) ?>"
                                    data-address="<?= htmlspecialchars($t['address']) ?>"
                                    data-position="<?= htmlspecialchars($t['position']) ?>"
                                    data-subject="<?= htmlspecialchars($t['subject_handled'] ?? '') ?>"
                                    data-adviser="<?= htmlspecialchars($t['adviser_grade'] ?? '') ?>"
                                    data-subjectgrades="<?= htmlspecialchars($t['subject_grades'] ?? '') ?>"
                                    data-toggle="modal" data-target="#editTeacherModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm py-0 px-2 delete-btn"
                                    data-id="<?= $t['id_user'] ?>"
                                    data-name="<?= htmlspecialchars($t['lname'].', '.$t['fname']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">No teachers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#0b2b5c,#0f3b7a);">
                <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit Teacher Record</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="edit_id_user">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="lname" id="edit_lname" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="fname" id="edit_fname" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Middle Initial</label>
                                <input type="text" class="form-control form-control-sm" name="mi" id="edit_mi" maxlength="5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm" name="age" id="edit_age" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Sex <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="sex" id="edit_sex" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Contact <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-sm" name="contact" id="edit_contact" maxlength="11" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm" name="email" id="edit_email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">
                                    New Password
                                    <span class="text-muted font-weight-normal">(leave blank to keep current)</span>
                                </label>
                                <input type="password" class="form-control form-control-sm" name="password" id="edit_password" placeholder="Leave blank to keep current">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="address" id="edit_address" required>
                    </div>
                    <hr class="my-2">
                    <p class="font-weight-bold small text-primary mb-2"><i class="fas fa-school mr-1"></i>School Assignment</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Position <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="position" id="edit_position" required>
                                    <option value="">— Select —</option>
                                    <option value="Teacher I">Teacher I</option>
                                    <option value="Teacher II">Teacher II</option>
                                    <option value="Teacher III">Teacher III</option>
                                    <option value="Master Teacher I">Master Teacher I</option>
                                    <option value="Master Teacher II">Master Teacher II</option>
                                    <option value="Head Teacher">Head Teacher</option>
                                    <option value="Principal">Principal</option>
                                    <option value="Registrar">Registrar</option>
                                    <option value="Guidance Counselor">Guidance Counselor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Subject Handled</label>
                                <input type="text" class="form-control form-control-sm" name="subject_handled" id="edit_subject">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Adviser of Grade/Section</label>
                                <select class="form-control form-control-sm" name="adviser_grade" id="edit_adviser">
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
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold small">Grade Levels as Subject Teacher</label>
                        <small class="text-muted d-block mb-2">Select all grade levels where this teacher handles subjects (separate from advisory class)</small>
                        <div class="row" id="subjectGradesCheckboxes">
                            <?php
                            $all_grades = [
                                'Grade 7','Grade 8','Grade 9','Grade 10',
                                'Grade 11 - STEM','Grade 11 - ABM','Grade 11 - GAS','Grade 11 - TVL-ICT','Grade 11 - TVL-HE',
                                'Grade 12 - STEM','Grade 12 - ABM','Grade 12 - GAS','Grade 12 - TVL-ICT','Grade 12 - TVL-HE',
                            ];
                            foreach ($all_grades as $gl):
                            ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input subject-grade-cb" type="checkbox"
                                        name="subject_grades[]"
                                        value="<?= $gl ?>"
                                        id="sg_<?= preg_replace('/[^a-z0-9]/i','_',$gl) ?>">
                                    <label class="form-check-label small" for="sg_<?= preg_replace('/[^a-z0-9]/i','_',$gl) ?>">
                                        <?= $gl ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <input type="hidden" name="role" value="staff">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_staff" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Update Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form method="post" id="deleteForm" style="display:none;">
    <input type="hidden" name="id_user" id="delete_id_user">
    <input type="hidden" name="delete_staff" value="1">
</form>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#teacherTable').DataTable({ pageLength: 15, order: [[1,'asc']], columnDefs: [{ orderable: false, targets: [9] }] });
});

$('.edit-btn').on('click', function() {
    const b = $(this);
    $('#edit_id_user').val(b.data('id'));
    $('#edit_lname').val(b.data('lname'));
    $('#edit_fname').val(b.data('fname'));
    $('#edit_mi').val(b.data('mi'));
    $('#edit_age').val(b.data('age'));
    $('#edit_sex').val(b.data('sex'));
    $('#edit_email').val(b.data('email'));
    $('#edit_contact').val(b.data('contact'));
    $('#edit_address').val(b.data('address'));
    $('#edit_position').val(b.data('position'));
    $('#edit_subject').val(b.data('subject'));
    $('#edit_adviser').val(b.data('adviser'));
    $('#edit_password').val(''); // always blank — leave empty to keep current

    // Restore subject_grades checkboxes
    const subjectGrades = (b.data('subjectgrades') || '').split(',').map(s => s.trim()).filter(Boolean);
    $('.subject-grade-cb').each(function() {
        $(this).prop('checked', subjectGrades.indexOf($(this).val()) !== -1);
    });
});

$(document).on('click', '.delete-btn', function() {
    const id   = $(this).data('id');
    const name = $(this).data('name');
    Swal.fire({
        title: 'Delete ' + name + '?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete'
    }).then(function(result) {
        if (result.isConfirmed) {
            $('#delete_id_user').val(id);
            $('#deleteForm').submit();
        }
    });
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>