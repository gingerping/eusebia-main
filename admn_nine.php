<?php 
    session_start();
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    require('classes/resident.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();
    $eusebia->delete_nine();
    $eusebia->approve_nine();
    $eusebia->reject_nine();
    $view = $eusebia->view_nine();
    $id_resident = $_GET['id_resident'] ?? null;
    $resident = $id_resident ? $residenteusebia->get_single_nine($id_resident) : null;
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .input-icons i { position: absolute; }
    .input-icons { width: 30%; margin-bottom: 10px; margin-left: 34%; }
    .icon { padding: 10px; min-width: 40px; }
    .form-control { text-align: center; }
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col text-center">
            <h1>GRADE 9 ENROLLEES</h1>
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
                <button class="btn btn-success" name="search_nine" style="width:90px;font-size:17px;border-radius:30px;margin-left:41.5%;">Search</button>
                <a href="admn_nine.php" class="btn btn-info" style="width:90px;font-size:17px;border-radius:30px;">Reload</a>
                <a href="admn_classlist.php?grade=nine" class="btn btn-secondary ml-2" style="font-size:15px;border-radius:30px;"><i class="fas fa-print mr-1"></i> Class List</a>
            </form>
            <br>
        </div>
    </div>

    <br>

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