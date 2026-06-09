<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require('classes/resident.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();
    $eusebia->delete_seven();
    $eusebia->approve_seven();
    $eusebia->reject_seven();
    $view = $eusebia->view_seven();
    $id_resident = $_GET['id_resident'] ?? null;
    $resident = $id_resident ? $residenteusebia->get_single_seven($id_resident) : null;
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

    <div class="row">
        <div class="col text-center">
            <h1>GRADE 7 ENROLLEES</h1>
        </div>
    </div>

    <hr><br><br>

    <div class="row">
        <div class="col">
            <form method="POST">
                <div class="input-icons">
                    <i class="fa fa-search icon"></i>
                    <input type="search" class="form-control" name="keyword" value="" required="" style="border-radius:30px;"/>
                </div>
                <button class="btn btn-success" name="search_seven" style="width:90px;font-size:17px;border-radius:30px;margin-left:41.5%;">Search</button>
                <a href="admn_seven.php" class="btn btn-info" style="width:90px;font-size:17px;border-radius:30px;">Reload</a>
                <a href="admn_classlist.php?grade=seven" class="btn btn-secondary ml-2" style="font-size:15px;border-radius:30px;"><i class="fas fa-print mr-1"></i> Class List</a>
            </form>
            <br>
        </div>
    </div>

    <br>

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