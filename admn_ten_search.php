<?php
    // require the database connection
    require 'classes/conn.php';
    
    // Updated trigger to search_nine
    if(isset($_POST['search_ten'])){
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
                $stmnt = $conn->prepare("SELECT * FROM `tbl_ten` WHERE `lname` LIKE '%$keyword%' or `fname` LIKE '%$keyword%' 
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
                            <input type="hidden" name="id_ten" value="<?= $view['id_ten'];?>">
                            <button class="btn btn-danger" type="submit" style="width: 90px; font-size: 17px; border-radius:30px;" name="delete_ten"> Archive </button>
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
                    <input type="hidden" name="id_ten" value="<?= $row['id_ten'];?>">
                    <button class="btn btn-danger" type="submit" name="delete_ten"> Archive </button>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php
    }
$conn = null;
?>