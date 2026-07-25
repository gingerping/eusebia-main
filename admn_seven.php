<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require('classes/student.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();
    $eusebia->delete_seven();
    $eusebia->approve_seven();
    $eusebia->reject_seven();
    $eusebia->admin_add_enrollee('seven');
    $view = $eusebia->view_seven();
    $id_student = $_GET['id_student'] ?? null;
    $student = $id_student ? $studenteusebia->get_single_seven($id_student) : null;
?>
<?php 
    include('dashboard_sidebar_start.php');
?>

<style>
    .input-icons i { position: absolute; }
    .input-icons { width: 30%; margin-bottom: 10px; margin-left: 34%; }
    .icon { padding: 10px; min-width: 40px; }
    .form-control { text-align: center; }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-users mr-2" style="color:#0b2b5c;"></i>Grade 7 — Student List
            </h4>
            <small class="text-muted">
                All enrollees &nbsp;|&nbsp; <?= is_array($view) ? count($view) : 0 ?> student(s)
            </small>
        </div>
        <div>
            <?php
                $grade = 'seven'; $gradeNumber = '7'; $hasCourse = false;
                include('admn_add_enrollee_modal.php');
            ?>
            <a href="admn_classlist.php?grade=seven" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Class List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php include('admn_table_seven_search.php'); ?>
        </div>
    </div>

</div>
<!-- End of Main Content -->

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