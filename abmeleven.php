<?php
    
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors',0);
   require('classes/resident.class.php');
   $userdetails = $residenteusebia->get_userdata();
   $residenteusebia->validate_admin();
   $eusebia->delete_eleven();
   $view = $residenteusebia->view_eleven_abm();
   
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

<!-- Begin Page Content -->

<div class="container-fluid">

    <!-- Page Heading -->

    <div class="row"> 
        <div class="col text-center"> 
            <h1> ABM Table</h1>
        </div>
    </div>

    <hr>
    <br><br>

    <div class="row"> 
        <div class="col"> 
            <?php
    // require the database connection
    require 'classes/conn.php';
    
    // Updated trigger to search_nine
    if(isset($_POST['search_eleven'])){
        $keyword = $_POST['keyword'];
?>
<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
            <tr>
                <th> LRN </th>
                <th> Course </th>
                <th> Full Name </th>
                <th> Birthday</th>
                <th> Age </th>
                <th> Contact </th>
                <th> Email </th>
                <th> Actions</th>
            </tr>
        </thead>
        <tbody>    
            <?php
                // Target tbl_nine for Grade 9 records
                $stmnt = $conn->prepare("SELECT * FROM `tbl_eleven` WHERE `lname` LIKE '%$keyword%' or `fname` LIKE '%$keyword%' 
                or `id_resident` LIKE '%$keyword%' or `lrn` LIKE '%$keyword%'");
                $stmnt->execute();
                
                while($view = $stmnt->fetch()){
            ?>
                <tr>
                    <td> <?= $view['lrn'];?> </td>
                    <td> <?= $view['course'];?> </td>  
                    <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?></td>
                    <td> <?= $view['bdate'];?> </td>
                    <td> <?= $view['age'];?> </td>
                    <td> <?= $view['contact'];?> </td>
                    <td> <?= $view['email'];?> </td>
                    <td>    
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewModalSearch<?= $view['id_resident'] ?>">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="id_eleven" value="<?= $view['id_eleven'];?>">
                            <button class="btn btn-danger" type="submit" style="width: 90px; font-size: 17px; border-radius:30px;" name="delete_eleven"> Archive </button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="viewModalSearch<?= $view['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Student Profile: <?= $view['fname'] ?></h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body text-left">
                                <p><strong>LRN:</strong> <?= $view['lrn'] ?></p>
                                <p><strong>Full Name:</strong> <?= $view['lname'] ?>, <?= $view['fname'] ?> <?= $view['mi'] ?></p>
                                <p><strong>Age:</strong> <?= $view['age'] ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php       
    } else { 
    // DEFAULT VIEW
?>

<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
             <tr>
                <th> LRN </th>
                <th> Course </th>
                <th> Full Name </th>
                <th> Birthday</th>
                <th> Age </th>
                <th> Contact </th>
                <th> Email </th>
                <th> Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php if(is_array($view)) { ?>
        <?php foreach($view as $row) { ?>
        <tr>
            <td> <?= $row['lrn'];?> </td>
            <td> <?= $row['course'];?> </td> 
            <td> <?= $row['lname'];?>, <?= $row['fname'];?> <?= $row['mi'];?></td>
            <td> <?= $row['bdate'];?> </td>
            <td> <?= $row['age'];?> </td>
            <td> <?= $row['contact'];?> </td>
            <td> <?= $row['email'];?> </td>
            <td>    
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                    <i class="fa fa-eye"></i> View
                </button>
                
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="id_eleven" value="<?= $row['id_eleven'];?>">
                    <button class="btn btn-danger" type="submit" name="delete_eleven"> Archive </button>
                </form>

                <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Student Information</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body text-left">
                                <p><strong>School Year:</strong> <?= $row['sy'] ?></p>
                                <p><strong>Course:</strong> <?= $row['course'] ?></p>
                                <p><strong>LRN:</strong> <?= $row['lrn'] ?></p>
                                <hr style="border: 2px solid black; opacity: 1;">
                                <h5> <strong>Personal Information</strong></h5>
                                <p><strong>Full Name:</strong> <?= $row['lname'] ?>, <?= $row['fname'] ?> <?= $row['mi'] ?></p>
                                <p><strong>Birthday:</strong> <?= $row['bdate'] ?></p>
                                <p><strong>Age:</strong> <?= $row['age'] ?></p>
                                <p><strong>Contact Number:</strong> <?= $row['contact'] ?></p>
                                <p><strong>Email:</strong> <?= $row['email'] ?></p>
                                <p><strong>Current Address:</strong> <?= $row['current_address'] ?></p>
                                <p><strong>Permanent Address:</strong> <?= $row['perm_address'] ?></p>
                                <hr style="border: 2px solid black; opacity: 1;">
                                <h5><strong>Father's Information</strong></h5>
                                <p><strong>Name:</strong> <?= $row['flname'] ?>, <?= $row['ffname'] ?> <?= $row['fmi'] ?></p>
                                <p><strong>Contact:</strong> <?= $row['contact_f'] ?></p>
                                <hr style="border: 2px solid black; opacity: 1;">
                                <h5><strong>Mother's Name</strong></h5>
                                <p><strong>Name:</strong> <?= $row['mlname'] ?>, <?= $row['mfname'] ?> <?= $row['mmi'] ?></p>
                                <p><strong>Contact:</strong> <?= $row['contact_m'] ?></p>
                                <hr style="border: 2px solid black; opacity: 1;">
                                <h5><strong>For Returning Learner:</strong></h5>
                                <p><strong>Last Grade Level Completed:</strong> <?= $row['lglc'] ?></p>
                                <p><strong>Last School Attended:</strong> <?= $row['lsa'] ?></p>
                                <p><strong>Last School Year Completed:</strong> <?= $row['lysc'] ?></p>
                                <p><strong>School Id:</strong> <?= $row['school_id'] ?></p>
                                <hr style="border: 2px solid black; opacity: 1;">
                            </div>
                        </div>
                    </div>
                </div>
            </td> 
        </tr>
        <?php } ?>
    <?php } ?>
</tbody>
    </table>
</div>



<?php
    }
$conn = null;
?>
        </div>
    </div>
    
</div>

<!-- End of Main Content -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
<link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">

<?php 
    include('dashboard_sidebar_end.php');
?>
