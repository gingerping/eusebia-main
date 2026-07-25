<?php
/**
 * Shared "Add Student" modal for the admin Grade 7-12 pages.
 *
 * Include this from admn_seven.php ... admn_twelve.php AFTER setting:
 *   $grade        = 'seven' | 'eight' | 'nine' | 'ten' | 'eleven' | 'twelve';
 *   $gradeNumber  = '7' .. '12';
 *   $hasCourse    = true|false;              // grades 9-12 have a course/strand select
 *   $courseOptions (only needed if $hasCourse is true) e.g.:
 *       $courseOptions = ['STEM' => 'STEM', 'ABM' => 'ABM', ...];
 *   $courseLabel  = 'Course' | 'Strand';     // label shown above the select
 */
$grade          = $grade ?? 'seven';
$gradeNumber    = $gradeNumber ?? '7';
$hasCourse      = $hasCourse ?? false;
$courseOptions  = $courseOptions ?? [];
$courseLabel    = $courseLabel ?? 'Course';
$modalId        = 'addEnrolleeModal_' . $grade;
?>

<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#<?= $modalId ?>">
    <i class="fas fa-user-plus mr-1"></i> Add Student
</button>

<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:800px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <form action="" method="post" id="addEnrolleeForm_<?= $grade ?>">
                <div class="modal-header" style="background:linear-gradient(135deg,#0b2b5c,#1f5a9e);color:#fff;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus mr-2"></i>Add Grade <?= htmlspecialchars($gradeNumber) ?> Student
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;"><span>&times;</span></button>
                </div>

                <div class="modal-body" style="max-height:70vh;overflow-y:auto;">

                    <input type="hidden" name="grade_table" value="<?= htmlspecialchars($grade) ?>">

                    <h6 class="font-weight-bold text-primary">Enrollment Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Student Type</label>
                            <select name="student_type" class="form-control">
                                <option value="new">New Student</option>
                                <option value="old">Old Student</option>
                                <option value="transferee">Transferee</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>School Year</label>
                            <select name="sy" class="form-control" required>
                                <option value="2026-2027">2026-2027</option>
                                <option value="2027-2028">2027-2028</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>LRN</label>
                            <input type="text" name="lrn" class="form-control" placeholder="Learner Reference No." required>
                        </div>
                    </div>

                    <?php if ($hasCourse): ?>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label><?= htmlspecialchars($courseLabel) ?></label>
                            <select name="course" class="form-control" required>
                                <option value="">-- Select <?= htmlspecialchars($courseLabel) ?> --</option>
                                <?php foreach ($courseOptions as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <hr>
                    <h6 class="font-weight-bold text-primary">Learner Information</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4"><label>Last Name</label><input type="text" name="lname" class="form-control" required></div>
                        <div class="form-group col-md-4"><label>First Name</label><input type="text" name="fname" class="form-control" required></div>
                        <div class="form-group col-md-4"><label>Middle Initial</label><input type="text" name="mi" class="form-control" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4"><label>Birthdate</label><input type="date" name="bdate" class="form-control" required></div>
                        <div class="form-group col-md-4">
                            <label>Sex</label>
                            <select name="sex" class="form-control" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4"><label>Age</label><input type="number" name="age" class="form-control" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Contact Number</label><input type="text" name="contact" class="form-control" required></div>
                        <div class="form-group col-md-6"><label>Email Address</label><input type="email" name="email" class="form-control" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Current Address</label><textarea name="current_address" class="form-control" rows="2" required></textarea></div>
                        <div class="form-group col-md-6"><label>Permanent Address</label><textarea name="perm_address" class="form-control" rows="2" required></textarea></div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-primary">Parent / Guardian</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-semibold">Father's Name</label>
                            <input type="text" name="ffname" class="form-control mb-2" placeholder="First Name" required>
                            <input type="text" name="flname" class="form-control mb-2" placeholder="Last Name" required>
                            <input type="text" name="fmi" class="form-control mb-2" placeholder="Middle Initial" required>
                            <input type="text" name="contact_f" class="form-control" placeholder="Contact No." required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-semibold">Mother's Maiden Name</label>
                            <input type="text" name="mfname" class="form-control mb-2" placeholder="First Name" required>
                            <input type="text" name="mlname" class="form-control mb-2" placeholder="Last Name" required>
                            <input type="text" name="mmi" class="form-control mb-2" placeholder="Middle Initial" required>
                            <input type="text" name="contact_m" class="form-control" placeholder="Contact No." required>
                        </div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-primary">Previous Education</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6"><input type="text" name="lglc" class="form-control" placeholder="Last Grade Level Completed" required></div>
                        <div class="form-group col-md-6"><input type="text" name="lsa" class="form-control" placeholder="Last School Attended" required></div>
                        <div class="form-group col-md-6"><input type="text" name="lysc" class="form-control" placeholder="Last School Year Completed" required></div>
                        <div class="form-group col-md-6"><input type="text" name="school_id" class="form-control" placeholder="School ID" required></div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-primary">Socioeconomic Information</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Indigenous People (IP) Member</label>
                            <select name="is_ip" class="form-control" onchange="document.getElementById('ipGroupDiv_<?= $grade ?>').style.display = this.value === 'Yes' ? '' : 'none';">
                                <option value="No" selected>No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6" id="ipGroupDiv_<?= $grade ?>" style="display:none;">
                            <label>IP Group / Tribe</label>
                            <input type="text" name="ip_group" class="form-control" placeholder="e.g. Agta, Dumagat, Igorot">
                        </div>
                        <div class="form-group col-md-6">
                            <label>4Ps Beneficiary</label>
                            <select name="is_4ps" class="form-control" onchange="document.getElementById('fourpsDiv_<?= $grade ?>').style.display = this.value === 'Yes' ? '' : 'none';">
                                <option value="No" selected>No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6" id="fourpsDiv_<?= $grade ?>" style="display:none;">
                            <label>4Ps Household ID</label>
                            <input type="text" name="fourps_id" class="form-control" placeholder="4Ps Household ID">
                        </div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-primary">Requirements</h6>
                    <p class="text-muted small mb-2">Instead of uploading files, just mark whether the learner's documents are complete, or note what's still missing.</p>
                    <div class="form-group">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="reqComplete_<?= $grade ?>" name="requirements_status" value="Complete" class="custom-control-input" checked
                                onchange="document.getElementById('missingDocsDiv_<?= $grade ?>').style.display='none';">
                            <label class="custom-control-label" for="reqComplete_<?= $grade ?>">Complete Requirements</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="reqIncomplete_<?= $grade ?>" name="requirements_status" value="Incomplete" class="custom-control-input"
                                onchange="document.getElementById('missingDocsDiv_<?= $grade ?>').style.display='';">
                            <label class="custom-control-label" for="reqIncomplete_<?= $grade ?>">Incomplete / Missing Documents</label>
                        </div>
                    </div>
                    <div class="form-group" id="missingDocsDiv_<?= $grade ?>" style="display:none;">
                        <label>Missing Documents / Notes</label>
                        <textarea name="missing_docs_note" class="form-control" rows="2"
                            placeholder="e.g. Missing PSA Birth Certificate and Good Moral Certificate"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="admin_add_enrollee" value="1" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
