<?php

    
    require('classes/main.class.php');
    $eusebia->admin_changepass();
    $userdetails = $eusebia->get_userdata();

?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change Password</title>
        
    </head>
    <body>

    <div class="container-fluid"> 
    <div class="row">
        <div class="col-md-6"> <form method="POST"> 
                <div class="mb-3">
                    <label class="form-label">Old Password</label>
                    <input type="password" name="oldpassword" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="newpassword" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Verify New Password</label>
                    <input type="password" name="checkpassword" class="form-control" required>
                </div>

                <<div class="text-center">
    <input type="hidden" name="id_admin" value="<?php echo $userdetails['id_admin']; ?>">
    
    <button type="submit" name="admin_changepass" class="btn btn-success px-4">
        Change Password
    </button>
</div>
            </form>
        </div>
    </div> 
</div>


    
        
    </body>
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
</html>