<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require('classes/main.class.php');
    $eusebia->create_admin(); 
    $userdetails = $eusebia->get_userdata();
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Admin</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New Administrator</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
    <div class="mb-3">
        <label>First Name</label>
        <input type="text" name="fname" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Middle Initial</label>
        <input type="text" name="mi" class="form-control" maxlength="2">
    </div>

    <div class="mb-3">
        <label>Last Name</label>
        <input type="text" name="lname" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email or Phone Number</label>
        <input type="text" name="login_identity" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control">
    <option value="administrator">Admin</option> <option value="Staff">Staff</option>
</select>
    </div>

    <button type="submit" name="add_admin" class="btn btn-primary">Create Admin</button>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>