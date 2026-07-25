<?php 

    require_once('main.class.php');
    

    class StudentClass extends EUSEBIAClass {
        //------------------------------------ STUDENT CRUD FUNCTIONS ----------------------------------------

        public function create_student() {
    if(isset($_POST['add_student'])) {
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
    
        
        
        $addedby = $_POST['addedby'] ?? 'Student';

        // Logic for Email vs Phone
        $email_to_save = NULL;
        $phone_to_save = NULL;

        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        // Check if Identity exists
        if ($this->check_student($login_identity) == 0) {
            if ($age < 18) {
                echo "<script>alert('Sorry, you are underaged.');</script>";
                return(0);
            }

            try {
                $connection = $this->openConn();
                $stmt = $connection->prepare("INSERT INTO tbl_student (
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
        public function view_student(){
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * from tbl_student");
            $stmt->execute();
            $view = $stmt->fetchAll();
            return $view;
        }

        public function update_student() {
            if (isset($_POST['update_student'])) {
                $id_student = $_GET['id_student'];
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
    $stmt = $connection->prepare("UPDATE tbl_student SET `password` =?, `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_student` = ?");
    
    $stmt->execute([$password, $lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $family_role, $role, $addedby, $id_student]);

} else {
    // 2. If password is empty, update everything EXCEPT the password column
    $stmt = $connection->prepare("UPDATE tbl_student SET `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_student` = ?");
    
    // Note: $password is removed from the array below
    $stmt->execute([$lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $familyrole, $role, $addedby, $id_student]);
}

$message2 = "Student Data Updated";
echo "<script type='text/javascript'>alert('$message2');</script>";
header("refresh: 0");
            }
        }


        public function delete_student(){
            $id_student = $_POST['id_student'];

            if(isset($_POST['delete_student'])) {
                $connection = $this->openConn();
                $stmt = $connection->prepare("DELETE FROM tbl_student where id_student = ?");
                $stmt->execute([$id_student]);

                $message2 = "Student Data Deleted";
                
                echo "<script type='text/javascript'>alert('$message2');</script>";
                header("Refresh:0");
            }
        }

    //-------------------------------- EXTRA FUNCTIONS FOR STUDENT CLASS ---------------------------------

    


    public function get_single_student($id_student){

        $id_student = $_GET['id_student'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_student where id_student = ?");
        $stmt->execute([$id_student]);
        $student = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $student;
        }
        else{
            return false;
        }
    }
   
    public function check_student($login_identity) {

        $connection = $this->openConn();
        // Check both email and phone_number columns so duplicates are caught
        // regardless of whether the user registered with an email or phone number
        $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE email = ? OR phone_number = ?");
        $stmt->Execute([$login_identity, $login_identity]);
        $total = $stmt->rowCount(); 

        return $total;
    }

    public function count_student() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_student");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();
        return $rescount;
    }

    public function check_household($lname, $mi) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE lname = ? AND mi = ?");
        $stmt->Execute([$lname, $mi]);
        $total = $stmt->rowCount(); 
        return $total;
    }

    public function view_household_list() {
        $lname = $_POST['lname'];
        $mi = $_POST['mi'];

        if(isset($_POST['search_household'])) {
            $connection = $this->openConn();
            $stmt1 = $connection->prepare("SELECT * FROM `tbl_student` WHERE `lname` LIKE '%$lname%' and  `mi` LIKE '%$mi%'");
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

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_student where sex = 'female'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

  

    public function count_member_student() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_student where family_role = 'Family Member'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function profile_update() {
        $id_student = $_GET['id_student'];
        $age = $_POST['age'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];

        if (isset($_POST['profile_update'])) {
           
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_student SET  `age` = ?,  `status` = ?, 
            `address` = ?, `contact` = ? WHERE id_student = ?");
            $stmt->execute([ $age, $status, $address,
            $contact, $id_student]);
               
            $message2 = "Student Profile Updated";
                
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("Refresh:0");

        }

    }
    

    //------------------------------------- STUDENT FILTERING QUERIES --------------------------------------

    public function view_student_minor(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE `age` <= 17");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_student_adult(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE `age` >= 18 AND `age` <= 59");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_student_senior(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE `age` >= 60");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function count_student_senior() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_student WHERE `age` >= 60");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }





    //-------------------------------------- EXTRA FUNCTIONS ------------------------------------------------

    public function student_changepass() {
    // 1. Only run logic if the form was actually submitted
    if(isset($_POST['student_changepass'])) {
        
        // Use ?? to prevent "Undefined index" notices
        // It's safer to get the ID from a session or a POST field rather than GET for a sensitive action
        $id_student = $_POST['id_student'] ?? $_GET['id_student'] ?? null;
        $oldpassword_input = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $checkpassword = $_POST['checkpassword'] ?? '';

        if (!$id_student) {
            echo "<script>alert('Error: Student ID is missing.');</script>";
            return;
        }

        $connection = $this->openConn();
        
        // 2. Fetch the hashed password from the database
        $stmt = $connection->prepare("SELECT `password` FROM tbl_student WHERE id_student = ?");
        $stmt->execute([$id_student]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Validation Logic
        if(!$result) {
            echo "<script>alert('Student not found.');</script>";
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
            
            $stmt = $connection->prepare("UPDATE tbl_student SET password = ? WHERE id_student = ?");
            $success = $stmt->execute([$hashed_password, $id_student]);
            
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

    public function view_student_household(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_student WHERE `family_role` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_student_voters(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_student WHERE `voter` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }
 public function view_eleven_stem(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_eleven 
         WHERE course = 'STEM' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_eleven_abm(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_eleven 
         WHERE course = 'ABM' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

    public function view_eleven_gas(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_eleven 
         WHERE course = 'GAS' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_eleven_ict(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_eleven 
         WHERE course = 'TVL-ICT' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_eleven_he(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_eleven 
         WHERE course = 'TVL-HE' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}
    public function view_twelve_abm(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_twelve 
         WHERE course = 'ABM' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_twelve_stem(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_twelve 
         WHERE course = 'STEM' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_twelve_gas(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_twelve 
         WHERE course = 'GAS' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_twelve_ict(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_twelve 
         WHERE course = 'TVL-ICT' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

public function view_twelve_he(){
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT * FROM tbl_twelve 
         WHERE course = 'TVL-HE' 
         AND (is_archived = 0 OR is_archived IS NULL)"
    );
    $stmt->execute();
    return $stmt->fetchAll();
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
        $stmt = $connection->prepare("SELECT * from tbl_student WHERE `fname` = '$search'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;

            


            
        
        

    }
    public function count_all_courses($table_name) {
    $connection = $this->openConn();
    $stmt = $connection->prepare(
        "SELECT course, COUNT(*) as total 
         FROM $table_name 
         WHERE (is_archived = 0 OR is_archived IS NULL)
         GROUP BY course
         ORDER BY course"
    );
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // returns ['CourseName' => count, ...]
}
public function count_by_grade($table_name, $column = null, $value = null) {
    $connection = $this->openConn();

    if ($column === null) {
        // Grade 7–10 (no strand)
        $stmt = $connection->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE is_archived = 0 OR is_archived IS NULL"
        );
        $stmt->execute();
    } else {
        // Grade 11–12 strands (STEM, ABM, GAS, TVL-ICT, TVL-HE)
        $stmt = $connection->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE $column = ? AND (is_archived = 0 OR is_archived IS NULL)"
        );
        $stmt->execute([$value]);
    }

    return $stmt->fetchColumn();
}





    }

    $studenteusebia = new StudentClass();
?>