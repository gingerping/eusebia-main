<?php 
    
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors',0);
    require('classes/resident.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();
    $eusebia->delete_nine();
    $view = $eusebia->view_nine();
    $id_resident = $_GET['id_resident'];
    $resident = $residenteusebia->get_single_nine($id_resident);
   
?>
<?php 
    include('dashboard_sidebar_start.php');
?>

<style>
    .input-icons i {
        position: absolute;
    }
        
    .input-icons {
        width: 30%;
        margin-bottom: 10px;
        margin-left: 34%;
    }
        
    .icon {
        padding: 10px;
        min-width: 40px;
    }
    .form-control{
        text-align: center;
    }
</style>

<div class="container-fluid">

    <div class="row"> 
        <div class="col text-center"> 
            <h1> GRADE 9 ENROLLEES</h1>
        </div>
    </div>

    <hr>
    <br><br>

    <div class="row">
        <div class="col">
            <form method="POST">
            <div class="input-icons" >
                <i class="fa fa-search icon"></i>
                <input type="search" class="form-control" name="keyword" value="" required="" style="border-radius: 30px;"/>
            </div>
                <button class="btn btn-success" name="search_nine" style="width: 90px; font-size: 17px; border-radius:30px; margin-left:41.5%;">
                    Search
                </button>
                <a href="admn_nine.php" class="btn btn-info" style="width: 90px; font-size: 17px; border-radius:30px;">Reload</a>
            </form>
            <br>
        </div>
    </div>

    <br>

    <div class="row"> 
        <div class="col-md-12"> 
            <?php 
                // Updated include to match the new naming convention
                include('admn_nine_search.php');
            ?>
        </div>
    </div>
    
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
<link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"> 
<script src="https://kit