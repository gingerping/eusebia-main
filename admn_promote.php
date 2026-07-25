<?php

    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    require('classes/student.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    // Handle promotion POST request
    $promote_result = null;
    if (isset($_POST['promote_grade'])) {
        $promote_result = $eusebia->promote_students(
            $_POST['from_grade'],
            $_POST['to_grade'],
            $_POST['new_sy'],
            $_POST['selected_ids'] ?? []
        );
    }

    // Load preview list based on selected "from_grade"
    $preview_grade = $_GET['from_grade'] ?? '';
    $preview_list = [];
    if ($preview_grade !== '') {
        $preview_list = $eusebia->get_students_for_promotion($preview_grade);
    }

    // Grade options
    $grade_map = [
        '7'  => ['label' => 'Grade 7',  'next' => '8'],
        '8'  => ['label' => 'Grade 8',  'next' => '9'],
        '9'  => ['label' => 'Grade 9',  'next' => '10'],
        '10' => ['label' => 'Grade 10', 'next' => '11'],
        '11' => ['label' => 'Grade 11', 'next' => '12'],
        '12' => ['label' => 'Grade 12', 'next' => null],
    ];

?>

<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .grade-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 12px rgba(11,43,92,0.10);
        transition: box-shadow 0.2s;
    }
    .grade-card:hover {
        box-shadow: 0 4px 24px rgba(11,43,92,0.18);
    }
    .arrow-badge {
        font-size: 2rem;
        color: #0b2b5c;
        font-weight: bold;
    }
    .promote-btn {
        background: linear-gradient(135deg, #0b2b5c, #0f3b7a);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 28px;
        font-size: 16px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .promote-btn:hover { opacity: 0.88; color: #fff; }
    .table-preview th { background: #0b2b5c; color: #fff; }
    .badge-grade {
        background: #0b2b5c;
        color: #fff;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 13px;
    }
    .check-all-label { font-weight: 600; color: #0b2b5c; cursor: pointer; }
</style>

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col text-center">
            <h1><i class="fas fa-level-up-alt mr-2"></i> GRADE PROMOTION</h1>
            <p class="text-muted">Move student records from one grade level to the next school year.</p>
        </div>
    </div>

    <hr>

    <?php if ($promote_result !== null): ?>
        <div class="alert alert-<?= $promote_result['success'] ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <strong><?= $promote_result['success'] ? '✅ Promotion Complete!' : '❌ Error' ?></strong>
            <?= htmlspecialchars($promote_result['message']) ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Step 1: Select Grade -->
    <div class="card grade-card mb-4">
        <div class="card-header" style="background: #0b2b5c; color:#fff; border-radius: 12px 12px 0 0;">
            <h5 class="mb-0"><i class="fas fa-filter mr-2"></i>Step 1 — Select Grade to Promote From</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="admn_promote.php">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Current Grade Level</label>
                        <select name="from_grade" class="form-control" required>
                            <option value="">-- Select Grade --</option>
                            <?php foreach ($grade_map as $g => $info): ?>
                                <?php if ($info['next'] !== null): ?>
                                    <option value="<?= $g ?>" <?= $preview_grade == $g ? 'selected' : '' ?>>
                                        <?= $info['label'] ?> → <?= $grade_map[$info['next']]['label'] ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn promote-btn mt-4">
                            <i class="fas fa-search mr-1"></i> Load Students
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Step 2: Preview & Confirm -->
    <?php if ($preview_grade !== '' && isset($grade_map[$preview_grade])): ?>
    <?php
        $next_grade = $grade_map[$preview_grade]['next'];
        $from_label = $grade_map[$preview_grade]['label'];
        $to_label   = $grade_map[$next_grade]['label'];
    ?>
    <div class="card grade-card mb-4">
        <div class="card-header" style="background: #0b2b5c; color:#fff; border-radius: 12px 12px 0 0;">
            <h5 class="mb-0">
                <i class="fas fa-users mr-2"></i>
                Step 2 — Preview: <span class="badge-grade"><?= $from_label ?></span>
                <span class="arrow-badge mx-2">→</span>
                <span class="badge-grade"><?= $to_label ?></span>
                <span class="float-right text-white-50" style="font-size:14px;">
                    <?= count($preview_list) ?> student(s) found
                </span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($preview_list)): ?>
                <div class="alert alert-info">No active students found in <?= $from_label ?>.</div>
            <?php else: ?>
            <form method="POST" action="admn_promote.php">
                <input type="hidden" name="from_grade" value="<?= $preview_grade ?>">
                <input type="hidden" name="to_grade" value="<?= $next_grade ?>">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">New School Year <span class="text-danger">*</span></label>
                        <input type="text" name="new_sy" class="form-control"
                               placeholder="e.g. 2026-2027" required
                               pattern="\d{4}-\d{4}"
                               title="Format: YYYY-YYYY">
                        <small class="text-muted">This will be applied to all promoted records.</small>
                    </div>
                    <?php if (in_array($next_grade, ['9','10','11','12'])): ?>
                    <div class="col-md-4">
                        <label class="font-weight-bold">Strand / Course for <?= $to_label ?></label>
                        <input type="text" name="default_course" class="form-control"
                               placeholder="e.g. STEM, ABM, TVL-ICT">
                        <small class="text-muted">Optional. Leave blank to keep existing or set per-student below.</small>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-2">
                    <label class="check-all-label">
                        <input type="checkbox" id="checkAll"> Select / Deselect All
                    </label>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm table-preview">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Select</th>
                                <th>LRN</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>MI</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>School Year</th>
                                <?php if (in_array($preview_grade, ['9','10','11','12'])): ?>
                                <th>Current Course</th>
                                <?php endif; ?>
                                <?php if (in_array($next_grade, ['9','10','11','12'])): ?>
                                <th>New Course</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($preview_list as $i => $s): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="text-center">
                                    <input type="checkbox" name="selected_ids[]"
                                           value="<?= htmlspecialchars($s['record_id']) ?>"
                                           class="student-check" checked>
                                </td>
                                <td><?= htmlspecialchars($s['lrn']) ?></td>
                                <td><?= htmlspecialchars($s['lname']) ?></td>
                                <td><?= htmlspecialchars($s['fname']) ?></td>
                                <td><?= htmlspecialchars($s['mi']) ?></td>
                                <td><?= htmlspecialchars($s['sex']) ?></td>
                                <td><?= htmlspecialchars($s['age']) ?></td>
                                <td><?= htmlspecialchars($s['sy']) ?></td>
                                <?php if (in_array($preview_grade, ['9','10','11','12'])): ?>
                                <td><?= htmlspecialchars($s['course'] ?? '—') ?></td>
                                <?php endif; ?>
                                <?php if (in_array($next_grade, ['9','10','11','12'])): ?>
                                <td>
                                    <input type="text"
                                           name="course_override[<?= htmlspecialchars($s['record_id']) ?>]"
                                           class="form-control form-control-sm"
                                           placeholder="e.g. STEM"
                                           value="<?= htmlspecialchars($s['course'] ?? '') ?>">
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (in_array($next_grade, ['9','10','11','12'])): ?>
                <!-- Pass course overrides as hidden JSON so PHP can process -->
                <input type="hidden" name="has_course_override" value="1">
                <?php endif; ?>

                <div class="mt-3 text-center">
                    <button type="submit" name="promote_grade" class="promote-btn btn"
                            onclick="return confirm('Promote selected students from <?= $from_label ?> to <?= $to_label ?>?\n\nThis will:\n• Copy their records into the <?= $to_label ?> table\n• Update the school year\n• Archive the old records\n\nThis cannot be undone automatically.')">
                        <i class="fas fa-level-up-alt mr-2"></i> Promote Selected Students
                    </button>
                    <a href="admn_promote.php" class="btn btn-secondary ml-3">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Grade Flow Overview -->
    <div class="card grade-card">
        <div class="card-header" style="background:#f8f9fc;">
            <h6 class="mb-0 text-secondary"><i class="fas fa-sitemap mr-2"></i>Grade Promotion Flow</h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center flex-wrap justify-content-center" style="gap:8px;">
                <?php foreach ($grade_map as $g => $info): ?>
                    <span class="badge-grade" style="padding:8px 18px; font-size:15px;"><?= $info['label'] ?></span>
                    <?php if ($info['next'] !== null): ?>
                        <span class="arrow-badge" style="font-size:1.4rem;">→</span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <p class="text-muted text-center mt-2 mb-0" style="font-size:13px;">
                Student records and all personal/family data are copied to the next grade table. The new school year is applied. Old records are archived.
            </p>
        </div>
    </div>

</div>

<script>
    // Check-all checkbox
    document.getElementById('checkAll') && document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<?php include('dashboard_sidebar_end.php'); ?>
