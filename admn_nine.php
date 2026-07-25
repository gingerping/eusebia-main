<?php 
    session_start();
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
    require('classes/student.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();
    $eusebia->delete_nine();
    $eusebia->approve_nine();
    $eusebia->reject_nine();
    $eusebia->admin_add_enrollee('nine');
    $view = $eusebia->view_nine();
    $id_student = $_GET['id_student'] ?? null;
    $student = $id_student ? $studenteusebia->get_single_nine($id_student) : null;
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .input-icons i { position: absolute; }
    .input-icons { width: 30%; margin-bottom: 10px; margin-left: 34%; }
    .icon { padding: 10px; min-width: 40px; }
    .form-control { text-align: center; }
</style>

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-users mr-2" style="color:#0b2b5c;"></i>Grade 9 — Student List
            </h4>
            <small class="text-muted">
                All enrollees &nbsp;|&nbsp; <?= is_array($view) ? count($view) : 0 ?> student(s)
            </small>
        </div>
        <div>
            <?php
                $grade = 'nine'; $gradeNumber = '9'; $hasCourse = true; $courseLabel = 'Course';
                $courseOptions = [
                    'ICT'        => 'ICT - Computer Programming',
                    'Animation'  => 'ICT - Animation',
                    'Cookery'    => 'Home Economics - Cookery',
                    'BAP'        => 'Home Economics - Bread and Pastry',
                    'Automotive' => 'Industrial Arts - Automotive',
                    'Welding'    => 'Industrial Arts - Welding (SMAW)',
                ];
                include('admn_add_enrollee_modal.php');
            ?>
            <a href="admn_classlist.php?grade=nine" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Class List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php include('admn_nine_search.php'); ?>
        </div>
    </div>

</div>

<?php if (!empty($_SESSION['swal'])):
    $swal = $_SESSION['swal'];
    unset($_SESSION['swal']);
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon:  '<?= $swal['icon'] ?>',
            title: '<?= addslashes($swal['title']) ?>',
            text:  '<?= addslashes($swal['text'] ?? '') ?>'
        });
    });
</script>
<?php endif; ?>

<?php include('dashboard_sidebar_end.php'); ?>