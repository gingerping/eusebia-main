<?php 

    require_once('main.class.php');
    

    class ResidentClass extends EUSEBIAClass {
        //------------------------------------ RESIDENT CRUD FUNCTIONS ----------------------------------------

        public function create_resident() {
    if(isset($_POST['add_resident'])) {
        // Use ?? '' to prevent "Undefined array key" errors
        $login_identity = $_POST['login_identity'] ?? ''; 
        $plain_password = $_POST['password'] ?? '';
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $age = $_POST['age'] ?? 0;
        $sex = $_POST['sex'] ?? '';
        $status = $_POST['status'] ?? '';
        $houseno = $_POST['houseno'] ?? '';
        $street = $_POST['street'] ?? '';
        $brgy = $_POST['brgy'] ?? '';
        $municipal = $_POST['municipal'] ?? '';
        $contact = $_POST['contact'] ?? ''; 
        $bdate = $_POST['bdate'] ?? '';
        $bplace = $_POST['bplace'] ?? '';
        $nationality = $_POST['nationality'] ?? '';
    
        
        
        $addedby = $_POST['addedby'] ?? 'Resident';

        // Logic for Email vs Phone
        $email_to_save = NULL;
        $phone_to_save = NULL;

        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        // Check if Identity exists
        if ($this->check_resident($login_identity) == 0) {
            if ($age < 18) {
                echo "<script>alert('Sorry, you are underaged.');</script>";
                return(0);
            }

            try {
                $connection = $this->openConn();
                $stmt = $connection->prepare("INSERT INTO tbl_resident (
                    `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, 
                    `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`, 
                    `bplace`, `nationality`, `addedby`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([ 
                    $email_to_save, $phone_to_save, $hashed_password, 
                    $lname, $fname, $mi, $age, $sex, $status, 
                    $houseno, $street, $brgy, $municipal, $contact, 
                    $bdate, $bplace, $nationality, $addedby
                ]);

                echo "<script>alert('Account added!'); window.location.href='index.php';</script>";
            } catch (PDOException $e) {
                // This will catch the "Column not found" error and tell you exactly what's wrong
                echo "Database Error: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('Email or Phone Number already registered.');</script>";
        }
    }
}
        public function view_resident(){
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * from tbl_resident");
            $stmt->execute();
            $view = $stmt->fetchAll();
            return $view;
        }

        public function update_resident() {
            if (isset($_POST['update_resident'])) {
                $id_resident = $_GET['id_resident'];
                $email = $_POST['email'];
                $password = ($_POST['password']);
                $lname = $_POST['lname'];
                $fname = $_POST['fname'];
                $mi = $_POST['mi'];
                $age = $_POST['age'];
                $sex = $_POST['sex'];
                $status = $_POST['status'];
                $houseno = $_POST['houseno'];
                $street = $_POST['street'];
                $brgy = $_POST['brgy'];
                $municipal = $_POST['municipal'];
                $contact = $_POST['contact'];
                $bdate = $_POST['bdate'];
                $bplace = $_POST['bplace'];
                $nationality = $_POST['nationality'];
                $voter = $_POST['voter'];
                $familyrole = $_POST['family_role'];
                $role = $_POST['role'];
                $addedby = $_POST['addedby'];

                $connection = $this->openConn();

// 1. Check if the password is being changed
if (!empty($password)) {
    // If password is NOT empty, update EVERYTHING including the new password
    $stmt = $connection->prepare("UPDATE tbl_resident SET `password` =?, `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    $stmt->execute([$password, $lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $family_role, $role, $addedby, $id_resident]);

} else {
    // 2. If password is empty, update everything EXCEPT the password column
    $stmt = $connection->prepare("UPDATE tbl_resident SET `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    // Note: $password is removed from the array below
    $stmt->execute([$lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $familyrole, $role, $addedby, $id_resident]);
}

$message2 = "Resident Data Updated";
echo "<script type='text/javascript'>alert('$message2');</script>";
header("refresh: 0");
            }
        }


        public function delete_resident(){
            $id_resident = $_POST['id_resident'];

            if(isset($_POST['delete_resident'])) {
                $connection = $this->openConn();
                $stmt = $connection->prepare("DELETE FROM tbl_resident where id_resident = ?");
                $stmt->execute([$id_resident]);

                $message2 = "Resident Data Deleted";
                
                echo "<script type='text/javascript'>alert('$message2');</script>";
                header("Refresh:0");
            }
        }

    //-------------------------------- EXTRA FUNCTIONS FOR RESIDENT CLASS ---------------------------------

    


    public function get_single_resident($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }
   
    public function check_resident($login_identity) {

        $connection = $this->openConn();
        // Check both email and phone_number columns so duplicates are caught
        // regardless of whether the user registered with an email or phone number
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE email = ? OR phone_number = ?");
        $stmt->Execute([$login_identity, $login_identity]);
        $total = $stmt->rowCount(); 

        return $total;
    }

    public function count_resident() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();
        return $rescount;
    }

    public function check_household($lname, $mi) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE lname = ? AND mi = ?");
        $stmt->Execute([$lname, $mi]);
        $total = $stmt->rowCount(); 
        return $total;
    }

    public function view_household_list() {
        $lname = $_POST['lname'];
        $mi = $_POST['mi'];

        if(isset($_POST['search_household'])) {
            $connection = $this->openConn();
            $stmt1 = $connection->prepare("SELECT * FROM `tbl_resident` WHERE `lname` LIKE '%$lname%' and  `mi` LIKE '%$mi%'");
            $stmt1->execute();
        }
    }

    public function count_eleven_stem() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_eleven where course = 'STEM' ");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function count_eleven_abm() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where sex = 'female'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

  

    public function count_member_resident() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where family_role = 'Family Member'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function profile_update() {
        $id_resident = $_GET['id_resident'];
        $age = $_POST['age'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];

        if (isset($_POST['profile_update'])) {
           
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_resident SET  `age` = ?,  `status` = ?, 
            `address` = ?, `contact` = ? WHERE id_resident = ?");
            $stmt->execute([ $age, $status, $address,
            $contact, $id_resident]);
               
            $message2 = "Resident Profile Updated";
                
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("Refresh:0");

        }

    }
    

    //------------------------------------- RESIDENT FILTERING QUERIES --------------------------------------

    public function view_resident_minor(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` <= 17");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_adult(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` >= 18 AND `age` <= 59");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_senior(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` >= 60");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function count_resident_senior() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident WHERE `age` >= 60");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }





    //-------------------------------------- EXTRA FUNCTIONS ------------------------------------------------

    public function resident_changepass() {
    // 1. Only run logic if the form was actually submitted
    if(isset($_POST['resident_changepass'])) {
        
        // Use ?? to prevent "Undefined index" notices
        // It's safer to get the ID from a session or a POST field rather than GET for a sensitive action
        $id_resident = $_POST['id_resident'] ?? $_GET['id_resident'] ?? null;
        $oldpassword_input = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $checkpassword = $_POST['checkpassword'] ?? '';

        if (!$id_resident) {
            echo "<script>alert('Error: Resident ID is missing.');</script>";
            return;
        }

        $connection = $this->openConn();
        
        // 2. Fetch the hashed password from the database
        $stmt = $connection->prepare("SELECT `password` FROM tbl_resident WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Validation Logic
        if(!$result) {
            echo "<script>alert('Resident not found.');</script>";
        } 
        // Use password_verify to check against the hashed DB password
        elseif (!password_verify($oldpassword_input, $result['password'])) {
            echo "<script>alert('Old Password is Incorrect');</script>";
        } 
        elseif ($newpassword !== $checkpassword) {
            echo "<script>alert('New Password and Verification Password do not Match');</script>";
        } 
        elseif (empty($newpassword)) {
            echo "<script>alert('New password cannot be empty');</script>";
        } 
        else {
            // 4. Update the password using a NEW hash
            $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);
            
            $stmt = $connection->prepare("UPDATE tbl_resident SET password = ? WHERE id_resident = ?");
            $success = $stmt->execute([$hashed_password, $id_resident]);
            
            if ($success) {
                echo "<script type='text/javascript'>
                        alert('Password Updated Successfully');
                        window.location.href = window.location.href; // Refresh page cleanly
                      </script>";
                exit();
            } else {
                echo "<script>alert('Database Error: Could not update password.');</script>";
            }
        }
    }
}



    //========================================== SCOPE CHANGED FUNCTIONS ===========================================

    public function view_resident_household(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `family_role` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_voters(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `voter` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }
    public function view_eleven_stem(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_eleven WHERE `course` = 'STEM'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_eleven_abm(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_eleven WHERE `course` = 'ABM'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_eleven_gas(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_eleven WHERE `course` = 'GAS'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_eleven_ict(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_eleven WHERE `course` = 'TVL-ICT'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_eleven_he(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_eleven WHERE `course` = 'TVL-HE'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }
    public function view_twelve_stem(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_twelve WHERE `course` = 'STEM'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_twelve_abm(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_twelve WHERE `course` = 'ABM'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_twelve_gas(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_twelve WHERE `course` = 'GAS'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_twelve_ict(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_twelve WHERE `course` = 'TVL-ICT'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_twelve_he(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_twelve WHERE `course` = 'TVL-HE'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

public function view_eleven($sort = 'lname', $order = 'ASC') {
    // 1. Whitelist (Security check)
    $allowed = ['lname', 'age', 'email', 'course', 'lrn'];
    if (!in_array($sort, $allowed)) { $sort = 'lname'; }
    
    // 2. Validate Order
    $order = ($order === 'DESC') ? 'DESC' : 'ASC';

    $connection = $this->openConn();
    // 3. Inject variables into the SQL
    $stmt = $connection->prepare("SELECT * FROM tbl_eleven ORDER BY $sort $order");
    $stmt->execute();
    return $stmt->fetchAll();
}
    
    

    public function search_admn_voter() {
        
        $search = $_GET['search'];

        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `fname` = '$search'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;

            


            
        
        

    }
public function count_by_grade($table_name, $column = null, $value = null) {
    $connection = $this->openConn();
    
    if ($column === null) {
        // Simple count for Grade 7, 8, 9, 10
        $stmt = $connection->prepare("SELECT COUNT(*) FROM $table_name");
        $stmt->execute();
    } else {
        // Specific count for Strands (STEM, ABM, etc.)
        $stmt = $connection->prepare("SELECT COUNT(*) FROM $table_name WHERE $column = ?");
        $stmt->execute([$value]);
    }
    
    return $stmt->fetchColumn();
}





    }

    $residenteusebia = new ResidentClass();
?>