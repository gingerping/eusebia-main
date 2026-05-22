
<?php
    ini_set('display_errors',0);
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/staff.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();
    $view = $staffbmis->view_staff();
    $staffbmis->create_staff();
    $upstaff = $staffbmis->update_staff();
    $staffbmis->delete_staff();
    $staffcount = $staffbmis->count_staff();
    
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
    // Always start sessions before accessing user data
    session_start();
    require('classes/main.class.php');
    
    // Create admin if none exist (as per your code)
    $bmis->create_admin();
    
    // Fetch user details
    $userdetails = $bmis->get_userdata();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5"> 
    <div class="row justify-content-center"> 
        <div class="col-md-6"> 
            <div class="card shadow p-4">
                <h4 class="mb-4">Add Administrator</h4>
                
                <form method="post"> 
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required> 
                    </div>

                    <div class="form-group">
                        <label>Firstname</label>
                        <input type="text" name="fname" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" name="lname" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="mi" class="form-control">
                    </div>

                    <input type="hidden" name="role" value="administrator"> 
                    
                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit" name="add_admin">Add Admin</button>

                        <?php if(isset($userdetails['id_admin'])): ?>
                            <a href="admin_changepass.php?id_admin=<?= $userdetails['id_admin']?>" class="btn btn-warning"> 
                                Change Password 
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
<!-- responsive tags for screen compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
<!-- custom css --> 
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
<!-- bootstrap css --> 
<link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"> 
<!-- fontawesome icons -->
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>

</body>
</html>