<?php 

class EUSEBIAClass {

//------------------------------------------ DATABASE CONNECTION ----------------------------------------------------
    
    protected $server = "mysql:host=localhost;dbname=eusebia";
    protected $user = "root";
    protected $pass = "";
    protected $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    protected $con;


    public function show_404()
    {
        http_response_code(404);
        echo "Page is currently unavailable";
        die;
    }

    public function openConn() {
        try {
            $this->con = new PDO($this->server, $this->user, $this->pass, $this->options);
            return $this->con;
        }

        catch(PDOException $e) {
            echo "Datbase Connection Error! ", $e->getMessage();
        }
    }

    //eto yung nag c close ng connection ng db
    public function closeConn() {
        $this->con = null;
    }


    //------------------------------------------ AUTHENTICATION & SESSION HANDLING --------------------------------------------
        //authentication function para sa sa tatlong type ng accounts
public function login() {
    if(isset($_POST['login'])) {
        $identity = $_POST['login_identity']; 
        $password_input = $_POST['password']; 
        $connection = $this->openConn();

        // 1. Check ADMIN - Only check EMAIL
        // 1. Check ADMIN - Now checking BOTH Email and Phone
$stmt = $connection->prepare("SELECT * FROM tbl_admin WHERE email = ? OR phone_number = ?");
$stmt->execute([$identity, $identity]); // We pass the same input to both '?' placeholders
$user = $stmt->fetch();

if($user && password_verify($password_input, $user['password'])) {
    $this->set_userdata($user);
    header('Location: admn_dashboard.php');
    exit(); 
}

        // 2. Check USER (Staff) - Only check EMAIL
        $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE email = ?");
        $stmt->execute([$identity]);
        $user = $stmt->fetch();

        if($user && password_verify($password_input, $user['password'])) {
            $this->set_userdata($user);
            echo "<script>window.location.href='staff_dashboard.php';</script>";
            exit(); 
        }

        // 3. Check RESIDENT - Check EMAIL OR PHONE_NUMBER
        // We only use phone_number here because we are sure this table has it.
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE email = ? OR phone_number = ?");
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        if($user && password_verify($password_input, $user['password'])) {
            $this->set_userdata($user);
            header('Location: resident_homepage.php');
            exit();
        }

        // Only shows if NONE of the above found a match
        echo "<script type='text/javascript'>alert('Invalid Credentials.');</script>";
    }
}

    //eto yung function na mag e end ng session tas i l logout ka 
    public function logout(){
        if(!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['userdata'] = null;
        unset($_SESSION['userdata']); 
        
    }

    // etong method na get_userdata() kukuha ng session mo na 'userdata' mo na i identify sino yung naka login sa site 
public function get_userdata() {
    
    // 1. Start session if it hasn't been started yet
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 2. Check if the key exists FIRST before returning it
    if (isset($_SESSION['userdata'])) {
        return $_SESSION['userdata'];
    } 

    // 3. Return null if no user data is found (e.g., not logged in)
    return null;
}

    //eto yung condition na mag s set userdata na gagamiting pagkakakilala sayo sa buong session kapag nag login in ka
    public function set_userdata($array) {

        //i ch check nito kung naka set naba yung session, kapag hindi pa naka set i r run niya yung session_start();
        if(!isset($_SESSION)) {
            session_start();
        }

        //eto si userdata yung mag s set ng name mo tsaka role/access habang ikaw ay nag b browse at gumagamit ng store management
        $_SESSION['userdata'] = array(
            "id_admin" => $array['id_admin'],
            "id_resident" => $array['id_resident'],
            "id_user" => $array['id_user'],
            "emailadd" => $array['email'],
            "password" => $array['password'],
            //"fullname" => $array['lname']. " ".$array['fname']. " ".$array['mi'],
            "surname" => $array['lname'],
            "firstname" => $array['fname'],
            "mname" => $array['mi'],
            "age" => $array['age'],
            "sex" => $array['sex'],
            "status" => $array['status'],
            "address" => $array['address'],
            "contact" => $array['contact'],
            "bdate" => $array['bdate'],
            "bplace" => $array['bplace'],
            "nationality" => $array['nationality'],
            "family_role" => $array['family_role'],
            "role" => $array['role'],
            "houseno" => $array['houseno'],
            "street" => $array['street'],
            "brgy" => $array['brgy'],
            "municipal" => $array['municipal'],
            // Staff/Teacher fields (null-safe for residents/admins)
            "lname"           => $array['lname']            ?? $array['surname']   ?? '',
            "fname"           => $array['fname']            ?? $array['firstname'] ?? '',
            "position"        => $array['position']         ?? '',
            "subject_handled" => $array['subject_handled']  ?? '',
            "adviser_grade"   => $array['adviser_grade']    ?? '',
            "subject_grades"  => $array['subject_grades']   ?? ''
        );
        return $_SESSION['userdata'];
    }



 //----------------------------------------------------- ADMIN CRUD ---------------------------------------------------------
  public function create_admin() {
    if(isset($_POST['add_admin'])) {
        // 1. Use ?? '' to prevent warnings if the field is missing from HTML
        $login_identity = $_POST['login_identity'] ?? ''; 
        $password_input = $_POST['password'] ?? '';
        
        // Hash the password for security
        $password = password_hash($password_input, PASSWORD_DEFAULT); 
        
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $role = $_POST['role'] ?? 'Admin';

        // 2. Logic to separate Email from Phone
        $email_to_save = NULL;
        $phone_to_save = NULL;

        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        // 3. Validation: Make sure the identity isn't empty
        if (empty($login_identity)) {
            echo "<script>alert('Please provide an email or phone number.');</script>";
            return;
        }

        if ($this->check_admin($login_identity) == 0 ) {
            $connection = $this->openConn();
            // Ensure phone_number column exists in tbl_admin or remove it from the query
            $stmt = $connection->prepare("INSERT INTO tbl_admin (`email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email_to_save, $phone_to_save, $password, $lname, $fname, $mi, $role]);
            
            echo "<script>alert('Administrator account added'); window.location.href='add_admin.php';</script>";
        } else {
            echo "<script>alert('Account already exists');</script>";
        }
    }
}

   public function admin_changepass() {
    if(isset($_POST['admin_changepass'])) {
        
        // 1. Capture the ID and password inputs
        $id_admin = $_POST['id_admin'] ?? null;
        $oldpassword = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $checkpassword = $_POST['checkpassword'] ?? '';

        if (empty($id_admin)) {
            echo "<script>alert('Error: Admin ID is missing. Please re-login.');</script>";
            return;
        }

        $connection = $this->openConn();
        
        // 2. Fetch the current hashed password from the database
        $stmt = $connection->prepare("SELECT `password` FROM tbl_admin WHERE id_admin = ?");
        $stmt->execute([$id_admin]);
        $result = $stmt->fetch();

        if (!$result) {
            echo "<script>alert('Admin user not found.');</script>";
            return;
        }

        // 3. Verify Old Password (checks input against the Bcrypt hash)
        if (!password_verify($oldpassword, $result['password'])) { 
            echo "<script>alert('Old Password is Incorrect');</script>";
        } 
        // 4. Ensure New Password and Confirm Password match
        elseif ($newpassword !== $checkpassword) {
            echo "<script>alert('New Passwords do not match');</script>";
        } 
        // 5. Ensure the new password isn't empty
        elseif (empty($newpassword)) {
            echo "<script>alert('New password cannot be empty');</script>";
        }
        else {
            // 6. Success: Hash the NEW password and update
            $hashed_new = password_hash($newpassword, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("UPDATE tbl_admin SET password = ? WHERE id_admin = ?");
            $stmt->execute([$hashed_new, $id_admin]);
            
            echo "<script type='text/javascript'>
                alert('Password Updated Successfully'); 
                window.location.href='admn_dashboard.php';
            </script>";
        }
    }
}


    public function check_admin($email) {

        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_admin WHERE email = ?");
        $stmt->Execute([$email]);
        $total = $stmt->rowCount(); 

        return $total;
    }

    //eto yung function na mag bibigay restriction sa mga admin pages
    public function validate_admin(){
        $userdetails = $this->get_userdata();

        if (isset($userdetails)) {
            
            if($userdetails['role'] != "administrator") {
                $this->show_404();
            }

            else {
                return $userdetails;
            }
        }
    }

    public function validate_staff() {

        if(isset($userdetails)) {
            if($userdetails['role'] != "administrator" || $userdetails['role'] != "user") {
                $this->show_404();
            }

            else {
                return $userdetails;
            }
        }
    }















    //----------------------------------------- DOCUMENT PROCESSING FUNCTIONS -------------------------------------
    //-------------------------------------------------------------------------------------------------------------

public function create_seven() {
    if(isset($_POST['create_seven'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? ''; 
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        // Add this to link the record to the logged-in user
        $id_resident = $_POST['id_resident'] ?? '';
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';
 
        // Handle multiple document uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/seven/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png',
                             'application/msword',
                             'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/seven/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;
 
        $connection = $this->openConn();
 
        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }
        
        // I have added `id_resident` here so you know which user owns the enrollment
        $query = "INSERT INTO tbl_seven (
            `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, 
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, 
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, 
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`,
            `is_ip`, `ip_group`, `is_4ps`, `fourps_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $stmt = $connection->prepare($query);
        
        // Ensure the count of elements in this array matches the number of '?' (30 total)
        $stmt->execute([
            $sy, $lrn, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
            $current_address, $perm_address, $ffname, $flname, $fmi, 
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
            $lsa, $lysc, $school_id, $id_resident, $documents_json,
            $is_ip, $ip_group, $is_4ps, $fourps_id
        ]);
 
        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 7 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade7.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit(); 
    }
}

public function get_single_seven($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_seven where id_resident = ?");
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


public function view_seven(){ // Changed name to match the table
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * from tbl_seven WHERE is_archived = 0 OR is_archived IS NULL");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function delete_seven(){
    if(isset($_POST['delete_seven'])) {
        $id_seven = $_POST['id_seven'];
        $connection = $this->openConn();
        $stmt = $connection->prepare("UPDATE tbl_seven SET is_archived = 1, archived_at = NOW() WHERE id_seven = ?");
        $stmt->execute([$id_seven]);
        header("Refresh:0");
    }
}
private function sendMail($toEmail, $toName, $subject, $htmlBody, $altBody = '') {
    require_once __DIR__ . '/../phpmailer/Exception.php';
    require_once __DIR__ . '/../phpmailer/PHPMailer.php';
    require_once __DIR__ . '/../phpmailer/SMTP.php';
 
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'eusebiahighschool@gmail.com';
        $mail->Password   = 'ilfb ajcy gaiy iybg';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
 
        $mail->setFrom('eusebiahighschool@gmail.com', 'Eusebia High School');
        $mail->addAddress($toEmail, $toName ?: 'Student');
 
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $altBody ?: strip_tags($htmlBody);
 
        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}
 
public function approve_seven() {
    if (!isset($_POST['approve_seven'])) return;
 
    $id_seven = $_POST['id_seven'] ?? null;
    if (!$id_seven) {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Invalid record.'];
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
 
    $connection = $this->openConn();
 
    try { $connection->exec("ALTER TABLE tbl_seven ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); }
    catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_seven ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); }
    catch (PDOException $e) {}
 
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_seven WHERE id_seven = ?");
    $fetch->execute([$id_seven]);
    $student = $fetch->fetch();
 
    $update = $connection->prepare("UPDATE tbl_seven SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_seven = ?");
    $update->execute([$id_seven]);
    $this->closeConn();
 
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '') . ' ' . ($student['lname'] ?? ''));
 
    if (!empty($email)) {
        $html = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 7 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br>
                <p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div>
        </div>";
 
        $alt = "Dear $name,\n\nYour Grade 7 enrollment has been APPROVED. Eusebia High School";
 
        $result = $this->sendMail($email, $name, 'Grade 7 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon' => 'success', 'title' => 'Approved!', 'text' => 'Enrollment approved and email sent to ' . $email];
        } else {
            $_SESSION['swal'] = ['icon' => 'warning', 'title' => 'Approved (Email Failed)', 'text' => 'Status updated but email could not be sent. ' . ($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'Approved!', 'text' => 'Enrollment approved. No email address on record.'];
    }
 
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}
 
public function reject_seven() {
    if (!isset($_POST['reject_seven'])) return;
 
    $id_seven      = $_POST['id_seven'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
 
    if (!$id_seven) {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Invalid record.'];
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
 
    $connection = $this->openConn();
 
    try { $connection->exec("ALTER TABLE tbl_seven ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); }
    catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_seven ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); }
    catch (PDOException $e) {}
 
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_seven WHERE id_seven = ?");
    $fetch->execute([$id_seven]);
    $student = $fetch->fetch();
 
    $update = $connection->prepare("UPDATE tbl_seven SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_seven = ?");
    $update->execute([$reject_reason, $id_seven]);
    $this->closeConn();
 
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '') . ' ' . ($student['lname'] ?? ''));
 
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason)
            ? "<p><strong>Reason:</strong> " . htmlspecialchars($reject_reason) . "</p>"
            : "";
        $reasonAlt = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
 
        $html = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 7 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br>
                <p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div>
        </div>";
 
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 7 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
 
        $result = $this->sendMail($email, $name, 'Grade 7 Enrollment Update – Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon' => 'info', 'title' => 'Rejected', 'text' => 'Enrollment rejected and email sent to ' . $email];
        } else {
            $_SESSION['swal'] = ['icon' => 'warning', 'title' => 'Rejected (Email Failed)', 'text' => 'Status updated but email could not be sent. ' . ($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon' => 'info', 'title' => 'Rejected', 'text' => 'Enrollment rejected. No email address on record.'];
    }
 
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}
 
 
public function view_archived_seven(){
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT *, 'Grade 7' AS grade_label, id_seven AS record_id, 'seven' AS grade_table FROM tbl_seven WHERE is_archived = 1");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function restore_seven(){
    if(isset($_POST['restore_seven'])) {
        $id_seven = $_POST['id_seven'];
        $connection = $this->openConn();
        $stmt = $connection->prepare("UPDATE tbl_seven SET is_archived = 0, archived_at = NULL WHERE id_seven = ?");
        $stmt->execute([$id_seven]);
        header("Location: admn_archive.php");
        exit();
    }
}
public function update_seven() {
    if (isset($_POST['update_seven'])) {
        $id_seven = $_GET['id_seven']; // Getting ID from URL
        $sy = $_POST['sy'];
        $lrn = $_POST['lrn'];
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $bdate = $_POST['bdate'];
        $sex = $_POST['sex'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $current_address = $_POST['current_address'];
        $perm_address = $_POST['perm_address'];
        $ffname = $_POST['ffname'];
        $flname = $_POST['flname'];
        $fmi = $_POST['fmi'];
        $contact_f = $_POST['contact_f']; 
        $mlname = $_POST['mlname'];
        $mfname = $_POST['mfname'];
        $mmi = $_POST['mmi'];
        $contact_m = $_POST['contact_m'];
        $lglc = $_POST['lglc'];
        $lsa = $_POST['lsa'];
        $lysc = $_POST['lysc'];
        $school_id = $_POST['school_id'];

        $connection = $this->openConn();
        // FIXED: Removed trailing comma before WHERE and corrected column names
        $stmt = $connection->prepare("UPDATE tbl_seven SET 
            sy = ?, lrn = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
            sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
            ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
            mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
            lysc = ?, school_id = ? 
            WHERE id_seven = ?");
            
        $stmt->execute([
            $sy, $lrn, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
            $current_address, $perm_address, $ffname, $flname, $fmi, 
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
            $lsa, $lysc, $school_id, $id_seven
        ]);
        
        echo "<script type='text/javascript'>alert('Grade 7 Data Updated');</script>";
        header("refresh: 0");
    }
}

public function create_eight() {
    if(isset($_POST['create_eight'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? ''; 
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        // Add this to link the record to the logged-in user
        $id_resident = $_POST['id_resident'] ?? '';
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';
        $prev_grade_table = trim($_POST['prev_grade_table'] ?? '');
        $prev_grade_id    = (int)($_POST['prev_grade_id']    ?? 0); 
 
        // Handle multiple document uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/eight/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png',
                             'application/msword',
                             'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/eight/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;
 
        $connection = $this->openConn();
 
        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }
        
        // I have added `id_resident` here so you know which user owns the enrollment
        // I have added `id_resident` here so you know which user owns the enrollment
        $query = "INSERT INTO tbl_eight (
            `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, 
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, 
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, 
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $stmt = $connection->prepare($query);
        
        // Exact 28 element balance mapping
        $stmt->execute([
            $sy, $lrn, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
            $current_address, $perm_address, $ffname, $flname, $fmi, 
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
            $lsa, $lysc, $school_id, $id_resident, $documents_json, $is_ip, $ip_group, $is_4ps, $fourps_id, $prev_grade_table, $prev_grade_id
        ]);
 
        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 8 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade8.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit(); 
    }
}
public function get_single_eight($id_resident){
    $id_resident = $_GET['id_resident'];
    
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_eight WHERE id_resident = ?");
    $stmt->execute([$id_resident]);
    $resident = $stmt->fetch();
    $total = $stmt->rowCount();

    if($total > 0 )  {
        return $resident;
    }
    else {
        return false;
    }
}

public function view_eight(){ 
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_eight WHERE is_archived = 0 OR is_archived IS NULL");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function delete_eight(){
    if(isset($_POST['delete_eight'])) {
        $id_eight = $_POST['id_eight']; 
        $connection = $this->openConn();
        $stmt = $connection->prepare("UPDATE tbl_eight SET is_archived = 1, archived_at = NOW() WHERE id_eight = ?");
        $stmt->execute([$id_eight]); 
        header("Refresh:0");
    }
}
public function approve_eight() {
    if (!isset($_POST['approve_eight'])) return;
    $id_eight = $_POST['id_eight'] ?? null;
    if (!$id_eight) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_eight ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_eight ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_eight WHERE id_eight = ?");
    $fetch->execute([$id_eight]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_eight SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_eight = ?");
    $update->execute([$id_eight]);

    // Permanently DELETE the previous grade record when Grade 8 enrollment is approved
    $prev_tbl = null;
    $prev_pk  = 0;
    $prev_stmt = $connection->prepare("SELECT prev_grade_table, prev_grade_id FROM `tbl_eight` WHERE `id_eight` = ?");
    $prev_stmt->execute([$id_eight]);
    $prev_row = $prev_stmt->fetch(PDO::FETCH_ASSOC);
    if ($prev_row) {
        $prev_tbl = $prev_row['prev_grade_table'];
        $prev_pk  = (int)$prev_row['prev_grade_id'];
    }
    $allowed_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
    if ($prev_tbl && $prev_pk > 0 && in_array($prev_tbl, $allowed_tables)) {
        $pk_map = [
            'tbl_seven'  => 'id_seven',
            'tbl_eight'  => 'id_eight',
            'tbl_nine'   => 'id_nine',
            'tbl_ten'    => 'id_ten',
            'tbl_eleven' => 'id_eleven',
            'tbl_twelve' => 'id_twelve',
        ];
        $prev_pk_col = $pk_map[$prev_tbl];
        // PERMANENTLY DELETE the previous grade record — NOT archived
        $delete_stmt = $connection->prepare(
            "DELETE FROM `{$prev_tbl}` WHERE `{$prev_pk_col}` = ?"
        );
        $delete_stmt->execute([$prev_pk]);
    }
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 8 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nYour Grade 8 enrollment has been APPROVED.\nPlease visit the school to complete your enrollment requirements.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 8 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function reject_eight() {
    if (!isset($_POST['reject_eight'])) return;
    $id_eight      = $_POST['id_eight'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
    if (!$id_eight) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_eight ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_eight ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_eight WHERE id_eight = ?");
    $fetch->execute([$id_eight]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_eight SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_eight = ?");
    $update->execute([$reject_reason, $id_eight]);
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason) ? "<p><strong>Reason:</strong> ".htmlspecialchars($reject_reason)."</p>" : "";
        $reasonAlt  = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 8 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 8 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 8 Enrollment Update  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function view_archived_eight(){
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT *, 'Grade 8' AS grade_label, id_eight AS record_id, 'eight' AS grade_table FROM tbl_eight WHERE is_archived = 1");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function restore_eight(){
    if(isset($_POST['restore_eight'])) {
        $id_eight = $_POST['id_eight'];
        $connection = $this->openConn();
        $stmt = $connection->prepare("UPDATE tbl_eight SET is_archived = 0, archived_at = NULL WHERE id_eight = ?");
        $stmt->execute([$id_eight]);
        header("Location: admn_archive.php");
        exit();
    }
}

public function update_eight() {
    if (isset($_POST['update_eight'])) {
        $id_eight = $_GET['id_eight']; 
        $sy = $_POST['sy'];
        $lrn = $_POST['lrn'];
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $bdate = $_POST['bdate'];
        $sex = $_POST['sex'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $current_address = $_POST['current_address'];
        $perm_address = $_POST['perm_address'];
        $ffname = $_POST['ffname'];
        $flname = $_POST['flname'];
        $fmi = $_POST['fmi'];
        $contact_f = $_POST['contact_f']; 
        $mlname = $_POST['mlname'];
        $mfname = $_POST['mfname'];
        $mmi = $_POST['mmi'];
        $contact_m = $_POST['contact_m'];
        $lglc = $_POST['lglc'];
        $lsa = $_POST['lsa'];
        $lysc = $_POST['lysc'];
        $school_id = $_POST['school_id'];

        $connection = $this->openConn();
        $stmt = $connection->prepare("UPDATE tbl_eight SET 
            sy = ?, lrn = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
            sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
            ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
            mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
            lysc = ?, school_id = ? 
            WHERE id_eight = ?");
            
        $stmt->execute([
            $sy, $lrn, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
            $current_address, $perm_address, $ffname, $flname, $fmi, 
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
            $lsa, $lysc, $school_id, $id_eight
        ]);
        
        echo "<script type='text/javascript'>alert('Grade 8 Data Updated');</script>";
        header("refresh: 0");
    }
}

public function create_nine() {
    if(isset($_POST['create_nine'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $course = $_POST['course'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? '';
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        $id_resident = $_POST['id_resident'] ?? '';
        $prev_grade_table = trim($_POST['prev_grade_table'] ?? '');
        $prev_grade_id    = (int)($_POST['prev_grade_id']    ?? 0);
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';

        // Handle multiple document/picture uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/nine/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/nine/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

        $connection = $this->openConn();

        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }

        // FIXED: Added 2 additional '?' tokens to hit exactly 29 parameters
        $query = "INSERT INTO tbl_nine (
            `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`,
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`,
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`,
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $connection->prepare($query);
        $stmt->execute([
            $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email,
            $current_address, $perm_address, $ffname, $flname, $fmi,
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc,
            $lsa, $lysc, $school_id, $id_resident, $documents_json, $is_ip, $ip_group, $is_4ps, $fourps_id, $prev_grade_table, $prev_grade_id
        ]);

        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 9 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade9.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit();
    }
}
    public function get_single_nine($id_resident){
        // Removed the $_GET overwrite so it uses the passed ID correctly
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_nine WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();

        return $resident ?: false;
    }

    public function view_nine(){ 
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_nine WHERE is_archived = 0 OR is_archived IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete_nine(){
        if(isset($_POST['delete_nine'])) {
            $id_nine = $_POST['id_nine']; 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_nine SET is_archived = 1, archived_at = NOW() WHERE id_nine = ?");
            $stmt->execute([$id_nine]); 
            header("Refresh:0");
            exit();
        }
    }
public function approve_nine() {
    if (!isset($_POST['approve_nine'])) return;
    $id_nine = $_POST['id_nine'] ?? null;
    if (!$id_nine) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_nine ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_nine ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_nine WHERE id_nine = ?");
    $fetch->execute([$id_nine]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_nine SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_nine = ?");
    $update->execute([$id_nine]);

    // Auto-archive the previous grade record when this enrollment is approved
    $prev_tbl = null;
    $prev_pk  = 0;
    $prev_stmt = $connection->prepare("SELECT prev_grade_table, prev_grade_id FROM `tbl_nine` WHERE `id_nine` = ?");
    $prev_stmt->execute([$id_nine]);
    $prev_row = $prev_stmt->fetch(PDO::FETCH_ASSOC);
    if ($prev_row) {
        $prev_tbl = $prev_row['prev_grade_table'];
        $prev_pk  = (int)$prev_row['prev_grade_id'];
    }
    $allowed_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
    if ($prev_tbl && $prev_pk > 0 && in_array($prev_tbl, $allowed_tables)) {
        $pk_map = [
            'tbl_seven'  => 'id_seven',
            'tbl_eight'  => 'id_eight',
            'tbl_nine'   => 'id_nine',
            'tbl_ten'    => 'id_ten',
            'tbl_eleven' => 'id_eleven',
            'tbl_twelve' => 'id_twelve',
        ];
        $prev_pk_col = $pk_map[$prev_tbl];
        $archive_stmt = $connection->prepare(
            "UPDATE `{$prev_tbl}` SET is_archived = 1, archived_at = NOW() WHERE `{$prev_pk_col}` = ?"
        );
        $archive_stmt->execute([$prev_pk]);
    }

    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 9 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nYour Grade 9 enrollment has been APPROVED.\nPlease visit the school to complete your enrollment requirements.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 9 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function reject_nine() {
    if (!isset($_POST['reject_nine'])) return;
    $id_nine       = $_POST['id_nine'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
    if (!$id_nine) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_nine ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_nine ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_nine WHERE id_nine = ?");
    $fetch->execute([$id_nine]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_nine SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_nine = ?");
    $update->execute([$reject_reason, $id_nine]);
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason) ? "<p><strong>Reason:</strong> ".htmlspecialchars($reject_reason)."</p>" : "";
        $reasonAlt  = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 9 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 9 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 9 Enrollment Update  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
    public function view_archived_nine(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT *, 'Grade 9' AS grade_label, id_nine AS record_id, 'nine' AS grade_table FROM tbl_nine WHERE is_archived = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function restore_nine(){
        if(isset($_POST['restore_nine'])) {
            $id_nine = $_POST['id_nine'];
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_nine SET is_archived = 0, archived_at = NULL WHERE id_nine = ?");
            $stmt->execute([$id_nine]);
            header("Location: admn_archive.php");
            exit();
        }
    }

    public function update_nine() {
        if (isset($_POST['update_nine'])) {
            // Get ID from the URL or hidden field
            $id_nine = $_GET['id_nine'] ?? $_POST['id_nine']; 
            
            $sy = $_POST['sy'];
            $lrn = $_POST['lrn'];
            $course = $_POST['course'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bdate = $_POST['bdate'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $current_address = $_POST['current_address'];
            $perm_address = $_POST['perm_address'];
            $ffname = $_POST['ffname'];
            $flname = $_POST['flname'];
            $fmi = $_POST['fmi'];
            $contact_f = $_POST['contact_f']; 
            $mlname = $_POST['mlname'];
            $mfname = $_POST['mfname'];
            $mmi = $_POST['mmi'];
            $contact_m = $_POST['contact_m'];
            $lglc = $_POST['lglc'];
            $lsa = $_POST['lsa'];
            $lysc = $_POST['lysc'];
            $school_id = $_POST['school_id'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_nine SET 
                sy = ?, lrn = ?, course = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
                sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
                ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
                mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
                lysc = ?, school_id = ? 
                WHERE id_nine = ?");
                
            $stmt->execute([
                $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
                $current_address, $perm_address, $ffname, $flname, $fmi, 
                $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
                $lsa, $lysc, $school_id, 
                $id_nine // Corrected: Used $id_nine instead of $id_eight
            ]);
            
            echo "<script type='text/javascript'>alert('Grade 9 Data Updated'); window.location.href='grade9.php';</script>";
            exit();
        }
    }

   public function create_ten() {
    if(isset($_POST['create_ten'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $course = $_POST['course'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? '';
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        $id_resident = $_POST['id_resident'] ?? '';
        $prev_grade_table = trim($_POST['prev_grade_table'] ?? '');
        $prev_grade_id    = (int)($_POST['prev_grade_id']    ?? 0);
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';

        // Handle multiple document/picture uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/ten/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/ten/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

        $connection = $this->openConn();

        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }

        $query = "INSERT INTO tbl_ten (
            `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`,
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`,
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`,
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $connection->prepare($query);
        $stmt->execute([
            $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email,
            $current_address, $perm_address, $ffname, $flname, $fmi,
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc,
            $lsa, $lysc, $school_id, $id_resident, $documents_json, $is_ip, $ip_group, $is_4ps, $fourps_id, $prev_grade_table, $prev_grade_id
        ]);

        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 10 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade10.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit();
    }
}

    public function get_single_ten($id_resident){
        // Removed the $_GET overwrite so it uses the passed ID correctly
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_ten WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();

        return $resident ?: false;
    }

    public function view_ten(){ 
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_ten WHERE is_archived = 0 OR is_archived IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete_ten(){
        if(isset($_POST['delete_ten'])) {
            $id_ten = $_POST['id_ten']; 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_ten SET is_archived = 1, archived_at = NOW() WHERE id_ten = ?");
            $stmt->execute([$id_ten]); 
            header("Refresh:0");
            exit();
        }
    }
public function approve_ten() {
    if (!isset($_POST['approve_ten'])) return;
    $id_ten = $_POST['id_ten'] ?? null;
    if (!$id_ten) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_ten ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_ten ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_ten WHERE id_ten = ?");
    $fetch->execute([$id_ten]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_ten SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_ten = ?");
    $update->execute([$id_ten]);

    // Auto-archive the previous grade record when this enrollment is approved
    $prev_tbl = null;
    $prev_pk  = 0;
    $prev_stmt = $connection->prepare("SELECT prev_grade_table, prev_grade_id FROM `tbl_ten` WHERE `id_ten` = ?");
    $prev_stmt->execute([$id_ten]);
    $prev_row = $prev_stmt->fetch(PDO::FETCH_ASSOC);
    if ($prev_row) {
        $prev_tbl = $prev_row['prev_grade_table'];
        $prev_pk  = (int)$prev_row['prev_grade_id'];
    }
    $allowed_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
    if ($prev_tbl && $prev_pk > 0 && in_array($prev_tbl, $allowed_tables)) {
        $pk_map = [
            'tbl_seven'  => 'id_seven',
            'tbl_eight'  => 'id_eight',
            'tbl_nine'   => 'id_nine',
            'tbl_ten'    => 'id_ten',
            'tbl_eleven' => 'id_eleven',
            'tbl_twelve' => 'id_twelve',
        ];
        $prev_pk_col = $pk_map[$prev_tbl];
        $archive_stmt = $connection->prepare(
            "UPDATE `{$prev_tbl}` SET is_archived = 1, archived_at = NOW() WHERE `{$prev_pk_col}` = ?"
        );
        $archive_stmt->execute([$prev_pk]);
    }

    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 10 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nYour Grade 10 enrollment has been APPROVED.\nPlease visit the school to complete your enrollment requirements.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 10 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function reject_ten() {
    if (!isset($_POST['reject_ten'])) return;
    $id_ten        = $_POST['id_ten'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
    if (!$id_ten) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_ten ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_ten ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_ten WHERE id_ten = ?");
    $fetch->execute([$id_ten]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_ten SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_ten = ?");
    $update->execute([$reject_reason, $id_ten]);
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason) ? "<p><strong>Reason:</strong> ".htmlspecialchars($reject_reason)."</p>" : "";
        $reasonAlt  = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 10 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 10 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 10 Enrollment Update  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
    public function view_archived_ten(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT *, 'Grade 10' AS grade_label, id_ten AS record_id, 'ten' AS grade_table FROM tbl_ten WHERE is_archived = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function restore_ten(){
        if(isset($_POST['restore_ten'])) {
            $id_ten = $_POST['id_ten'];
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_ten SET is_archived = 0, archived_at = NULL WHERE id_ten = ?");
            $stmt->execute([$id_ten]);
            header("Location: admn_archive.php");
            exit();
        }
    }

    public function update_ten() {
        if (isset($_POST['update_ten'])) {
            // Get ID from the URL or hidden field
            $id_ten = $_GET['id_ten'] ?? $_POST['id_ten']; 
            
            $sy = $_POST['sy'];
            $lrn = $_POST['lrn'];
            $course = $_POST['course'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bdate = $_POST['bdate'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $current_address = $_POST['current_address'];
            $perm_address = $_POST['perm_address'];
            $ffname = $_POST['ffname'];
            $flname = $_POST['flname'];
            $fmi = $_POST['fmi'];
            $contact_f = $_POST['contact_f']; 
            $mlname = $_POST['mlname'];
            $mfname = $_POST['mfname'];
            $mmi = $_POST['mmi'];
            $contact_m = $_POST['contact_m'];
            $lglc = $_POST['lglc'];
            $lsa = $_POST['lsa'];
            $lysc = $_POST['lysc'];
            $school_id = $_POST['school_id'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_ten SET 
                sy = ?, lrn = ?, course = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
                sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
                ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
                mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
                lysc = ?, school_id = ? 
                WHERE id_ten = ?");
                
            $stmt->execute([
                $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
                $current_address, $perm_address, $ffname, $flname, $fmi, 
                $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
                $lsa, $lysc, $school_id, 
                $id_ten // Corrected: Used $id_nine instead of $id_eight
            ]);
            
            echo "<script type='text/javascript'>alert('Grade 10 Data Updated'); window.location.href='grade10.php';</script>";
            exit();
        }
    } 

   public function create_eleven() {
    if(isset($_POST['create_eleven'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $course = $_POST['course'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? '';
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        $id_resident = $_POST['id_resident'] ?? '';
        $prev_grade_table = trim($_POST['prev_grade_table'] ?? '');
        $prev_grade_id    = (int)($_POST['prev_grade_id']    ?? 0);
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';

        // Handle multiple document/picture uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/eleven/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/eleven/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

        $connection = $this->openConn();

        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }

        $query = "INSERT INTO tbl_eleven (
            `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`,
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`,
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`,
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $connection->prepare($query);
        $stmt->execute([
            $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email,
            $current_address, $perm_address, $ffname, $flname, $fmi,
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc,
            $lsa, $lysc, $school_id, $id_resident, $documents_json, $is_ip, $ip_group, $is_4ps, $fourps_id, $prev_grade_table, $prev_grade_id
        ]);

        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 11 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade11.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit();
    }
}

    public function get_single_eleven($id_resident){
        // Removed the $_GET overwrite so it uses the passed ID correctly
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_eleven WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();

        return $resident ?: false;
    }
    public function view_eleven(){ 
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_eleven WHERE is_archived = 0 OR is_archived IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete_eleven(){
        if(isset($_POST['delete_eleven'])) {
            $id_eleven = $_POST['id_eleven']; 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_eleven SET is_archived = 1, archived_at = NOW() WHERE id_eleven = ?");
            $stmt->execute([$id_eleven]); 
            header("Refresh:0");
            exit();
        }
    }
public function approve_eleven() {
    if (!isset($_POST['approve_eleven'])) return;
    $id_eleven = $_POST['id_eleven'] ?? null;
    if (!$id_eleven) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_eleven ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_eleven ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_eleven WHERE id_eleven = ?");
    $fetch->execute([$id_eleven]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_eleven SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_eleven = ?");
    $update->execute([$id_eleven]);

    // Auto-archive the previous grade record when this enrollment is approved
    $prev_tbl = null;
    $prev_pk  = 0;
    $prev_stmt = $connection->prepare("SELECT prev_grade_table, prev_grade_id FROM `tbl_eleven` WHERE `id_eleven` = ?");
    $prev_stmt->execute([$id_eleven]);
    $prev_row = $prev_stmt->fetch(PDO::FETCH_ASSOC);
    if ($prev_row) {
        $prev_tbl = $prev_row['prev_grade_table'];
        $prev_pk  = (int)$prev_row['prev_grade_id'];
    }
    $allowed_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
    if ($prev_tbl && $prev_pk > 0 && in_array($prev_tbl, $allowed_tables)) {
        $pk_map = [
            'tbl_seven'  => 'id_seven',
            'tbl_eight'  => 'id_eight',
            'tbl_nine'   => 'id_nine',
            'tbl_ten'    => 'id_ten',
            'tbl_eleven' => 'id_eleven',
            'tbl_twelve' => 'id_twelve',
        ];
        $prev_pk_col = $pk_map[$prev_tbl];
        $archive_stmt = $connection->prepare(
            "UPDATE `{$prev_tbl}` SET is_archived = 1, archived_at = NOW() WHERE `{$prev_pk_col}` = ?"
        );
        $archive_stmt->execute([$prev_pk]);
    }

    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 11 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nYour Grade 11 enrollment has been APPROVED.\nPlease visit the school to complete your enrollment requirements.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 11 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function reject_eleven() {
    if (!isset($_POST['reject_eleven'])) return;
    $id_eleven     = $_POST['id_eleven'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
    if (!$id_eleven) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_eleven ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_eleven ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_eleven WHERE id_eleven = ?");
    $fetch->execute([$id_eleven]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_eleven SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_eleven = ?");
    $update->execute([$reject_reason, $id_eleven]);
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason) ? "<p><strong>Reason:</strong> ".htmlspecialchars($reject_reason)."</p>" : "";
        $reasonAlt  = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 11 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 11 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 11 Enrollment Update  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
    public function view_archived_eleven(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT *, 'Grade 11' AS grade_label, id_eleven AS record_id, 'eleven' AS grade_table FROM tbl_eleven WHERE is_archived = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function restore_eleven(){
        if(isset($_POST['restore_eleven'])) {
            $id_eleven = $_POST['id_eleven'];
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_eleven SET is_archived = 0, archived_at = NULL WHERE id_eleven = ?");
            $stmt->execute([$id_eleven]);
            header("Location: admn_archive.php");
            exit();
        }
    }

    public function update_eleven() {
        if (isset($_POST['update_eleven'])) {
            // Get ID from the URL or hidden field
            $id_eleven = $_GET['id_eleven'] ?? $_POST['id_eleven']; 
            
            $sy = $_POST['sy'];
            $lrn = $_POST['lrn'];
            $course = $_POST['course'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bdate = $_POST['bdate'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $current_address = $_POST['current_address'];
            $perm_address = $_POST['perm_address'];
            $ffname = $_POST['ffname'];
            $flname = $_POST['flname'];
            $fmi = $_POST['fmi'];
            $contact_f = $_POST['contact_f']; 
            $mlname = $_POST['mlname'];
            $mfname = $_POST['mfname'];
            $mmi = $_POST['mmi'];
            $contact_m = $_POST['contact_m'];
            $lglc = $_POST['lglc'];
            $lsa = $_POST['lsa'];
            $lysc = $_POST['lysc'];
            $school_id = $_POST['school_id'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_eleven SET 
                sy = ?, lrn = ?, course = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
                sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
                ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
                mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
                lysc = ?, school_id = ? 
                WHERE id_eleven = ?");
                
            $stmt->execute([
                $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
                $current_address, $perm_address, $ffname, $flname, $fmi, 
                $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
                $lsa, $lysc, $school_id, 
                $id_eleven // Corrected: Used $id_nine instead of $id_eight
            ]);
            
            echo "<script type='text/javascript'>alert('Grade 11 Data Updated'); window.location.href='grade11.php';</script>";
            exit();
        }
    }

     public function create_twelve() {
    if(isset($_POST['create_twelve'])) {
        $sy = $_POST['sy'] ?? '';
        $lrn = $_POST['lrn'] ?? '';
        $course = $_POST['course'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $mi = $_POST['mi'] ?? '';
        $bdate = $_POST['bdate'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_address = $_POST['current_address'] ?? '';
        $perm_address = $_POST['perm_address'] ?? '';
        $ffname = $_POST['ffname'] ?? '';
        $flname = $_POST['flname'] ?? '';
        $fmi = $_POST['fmi'] ?? '';
        $contact_f = $_POST['contact_f'] ?? '';
        $mlname = $_POST['mlname'] ?? '';
        $mfname = $_POST['mfname'] ?? '';
        $mmi = $_POST['mmi'] ?? '';
        $contact_m = $_POST['contact_m'] ?? '';
        $lglc = $_POST['lglc'] ?? '';
        $lsa = $_POST['lsa'] ?? '';
        $lysc = $_POST['lysc'] ?? '';
        $school_id = $_POST['school_id'] ?? '';
        $id_resident = $_POST['id_resident'] ?? '';
        $prev_grade_table = trim($_POST['prev_grade_table'] ?? '');
        $prev_grade_id    = (int)($_POST['prev_grade_id']    ?? 0);
        $is_ip    = $_POST['is_ip']    ?? 'No';
        $ip_group = ($is_ip === 'Yes') ? ($_POST['ip_group'] ?? '') : '';
        $is_4ps   = $_POST['is_4ps']   ?? 'No';
        $fourps_id = ($is_4ps === 'Yes') ? ($_POST['fourps_id'] ?? '') : '';

        // Handle multiple document/picture uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/documents/twelve/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $maxSize = 5 * 1024 * 1024; // 5MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $ftype = mime_content_type($tmpName);
                if (!in_array($ftype, $allowedTypes)) continue;
                $origName = basename($_FILES['documents']['name'][$idx]);
                $safeName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $dest = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/documents/twelve/' . $safeName;
                }
            }
        }
        $documents_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

        $connection = $this->openConn();

        // LRN duplicate check — only for new students (old/transferee re-use their existing LRN)
        $student_type = trim($_POST['student_type'] ?? 'new');
        if ($student_type === 'new') {
            $lrn_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
            $lrn_taken = false;
            foreach ($lrn_tables as $_lrn_tbl) {
                $lrn_stmt = $connection->prepare("SELECT COUNT(*) FROM `{$_lrn_tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
                $lrn_stmt->execute([trim($lrn)]);
                if ($lrn_stmt->fetchColumn() > 0) { $lrn_taken = true; break; }
            }
            if ($lrn_taken) {
                $safe_lrn = urlencode(trim($lrn));
                $ref = $_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()';
                $sep = (strpos($ref, '?') !== false) ? '&' : '?';
                header('Location: ' . $ref . $sep . 'lrn_error=' . $safe_lrn);
                exit();
            }
        }

        $query = "INSERT INTO tbl_twelve (
            `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`,
            `current_address`, `perm_address`, `ffname`, `flname`, `fmi`,
            `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`,
            `lsa`, `lysc`, `school_id`, `id_resident`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $connection->prepare($query);
        $stmt->execute([
            $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email,
            $current_address, $perm_address, $ffname, $flname, $fmi,
            $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc,
            $lsa, $lysc, $school_id, $id_resident, $documents_json, $is_ip, $ip_group, $is_4ps, $fourps_id, $prev_grade_table, $prev_grade_id
        ]);

        echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
<script>
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        Swal.fire({
            icon: 'success',
            title: 'Submitted!',
            text: 'Grade 12 Enrollment Submitted Successfully',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        }).then(function() {
            window.location.href = 'grade12.php';
        });
    };
    document.head.appendChild(script);
</script>";
        exit();
    }
}
    public function get_single_twelve($id_resident){
        // Removed the $_GET overwrite so it uses the passed ID correctly
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_twelve WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();

        return $resident ?: false;
    }

    public function view_twelve(){ 
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_twelve WHERE is_archived = 0 OR is_archived IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete_twelve(){
        if(isset($_POST['delete_twelve'])) {
            $id_twelve = $_POST['id_twelve']; 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_twelve SET is_archived = 1, archived_at = NOW() WHERE id_twelve = ?");
            $stmt->execute([$id_twelve]); 
            header("Refresh:0");
            exit();
        }
    }
public function approve_twelve() {
    if (!isset($_POST['approve_twelve'])) return;
    $id_twelve = $_POST['id_twelve'] ?? null;
    if (!$id_twelve) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_twelve ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_twelve ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_twelve WHERE id_twelve = ?");
    $fetch->execute([$id_twelve]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_twelve SET enrollment_status = 'Approved', reject_reason = NULL WHERE id_twelve = ?");
    $update->execute([$id_twelve]);

    // Auto-archive the previous grade record when this enrollment is approved
    $prev_tbl = null;
    $prev_pk  = 0;
    $prev_stmt = $connection->prepare("SELECT prev_grade_table, prev_grade_id FROM `tbl_twelve` WHERE `id_twelve` = ?");
    $prev_stmt->execute([$id_twelve]);
    $prev_row = $prev_stmt->fetch(PDO::FETCH_ASSOC);
    if ($prev_row) {
        $prev_tbl = $prev_row['prev_grade_table'];
        $prev_pk  = (int)$prev_row['prev_grade_id'];
    }
    $allowed_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];
    if ($prev_tbl && $prev_pk > 0 && in_array($prev_tbl, $allowed_tables)) {
        $pk_map = [
            'tbl_seven'  => 'id_seven',
            'tbl_eight'  => 'id_eight',
            'tbl_nine'   => 'id_nine',
            'tbl_ten'    => 'id_ten',
            'tbl_eleven' => 'id_eleven',
            'tbl_twelve' => 'id_twelve',
        ];
        $prev_pk_col = $pk_map[$prev_tbl];
        $archive_stmt = $connection->prepare(
            "UPDATE `{$prev_tbl}` SET is_archived = 1, archived_at = NOW() WHERE `{$prev_pk_col}` = ?"
        );
        $archive_stmt->execute([$prev_pk]);
    }

    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0b2b5c;'>&#127881; Enrollment Approved!</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We are pleased to inform you that your <strong>Grade 12 enrollment</strong> has been
                   <span style='color:#28a745;font-weight:bold;'>APPROVED</span>.</p>
                <p>Please visit the school to complete your enrollment requirements and for further instructions.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nYour Grade 12 enrollment has been APPROVED.\nPlease visit the school to complete your enrollment requirements.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 12 Enrollment Approved  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved!','text'=>'Enrollment approved. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
 
public function reject_twelve() {
    if (!isset($_POST['reject_twelve'])) return;
    $id_twelve     = $_POST['id_twelve'] ?? null;
    $reject_reason = trim($_POST['reject_reason'] ?? '');
    if (!$id_twelve) {
        $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid record.'];
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
    $connection = $this->openConn();
    try { $connection->exec("ALTER TABLE tbl_twelve ADD COLUMN enrollment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'"); } catch (PDOException $e) {}
    try { $connection->exec("ALTER TABLE tbl_twelve ADD COLUMN reject_reason TEXT NULL DEFAULT NULL"); } catch (PDOException $e) {}
    $fetch = $connection->prepare("SELECT email, fname, lname FROM tbl_twelve WHERE id_twelve = ?");
    $fetch->execute([$id_twelve]);
    $student = $fetch->fetch();
    $update = $connection->prepare("UPDATE tbl_twelve SET enrollment_status = 'Rejected', reject_reason = ? WHERE id_twelve = ?");
    $update->execute([$reject_reason, $id_twelve]);
    $this->closeConn();
    $email = $student['email'] ?? '';
    $name  = trim(($student['fname'] ?? '').' '.($student['lname'] ?? ''));
    if (!empty($email)) {
        $reasonHtml = !empty($reject_reason) ? "<p><strong>Reason:</strong> ".htmlspecialchars($reject_reason)."</p>" : "";
        $reasonAlt  = !empty($reject_reason) ? "\nReason: $reject_reason\n" : "";
        $html = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                <p style='color:#a8c4e0;margin:4px 0 0;'>Enrollment Notification</p>
            </div>
            <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                <h3 style='color:#c0392b;'>Enrollment Not Approved</h3>
                <p>Dear <strong>".htmlspecialchars($name)."</strong>,</p>
                <p>We regret to inform you that your <strong>Grade 12 enrollment</strong> has been
                   <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                {$reasonHtml}
                <p>If you have questions or would like to appeal, please visit the school during office hours.</p>
                <br><p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
            </div></div>";
        $alt = "Dear $name,\n\nWe regret to inform you that your Grade 12 enrollment has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\n– Eusebia High School";
        $result = $this->sendMail($email, $name, 'Grade 12 Enrollment Update  Eusebia High School', $html, $alt);
        if ($result['success']) {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected and email sent to '.$email];
        } else {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)','text'=>'Status updated but email could not be sent. '.($result['error'] ?? '')];
        }
    } else {
        $_SESSION['swal'] = ['icon'=>'info','title'=>'Rejected','text'=>'Enrollment rejected. No email address on record.'];
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
    public function view_archived_twelve(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT *, 'Grade 12' AS grade_label, id_twelve AS record_id, 'twelve' AS grade_table FROM tbl_twelve WHERE is_archived = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function restore_twelve(){
        if(isset($_POST['restore_twelve'])) {
            $id_twelve = $_POST['id_twelve'];
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_twelve SET is_archived = 0, archived_at = NULL WHERE id_twelve = ?");
            $stmt->execute([$id_twelve]);
            header("Location: admn_archive.php");
            exit();
        }
    }

    public function update_twelve() {
        if (isset($_POST['update_twelve'])) {
            // Get ID from the URL or hidden field
            $id_twelve = $_GET['id_twelve'] ?? $_POST['id_twelve']; 
            
            $sy = $_POST['sy'];
            $lrn = $_POST['lrn'];
            $course = $_POST['course'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bdate = $_POST['bdate'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $current_address = $_POST['current_address'];
            $perm_address = $_POST['perm_address'];
            $ffname = $_POST['ffname'];
            $flname = $_POST['flname'];
            $fmi = $_POST['fmi'];
            $contact_f = $_POST['contact_f']; 
            $mlname = $_POST['mlname'];
            $mfname = $_POST['mfname'];
            $mmi = $_POST['mmi'];
            $contact_m = $_POST['contact_m'];
            $lglc = $_POST['lglc'];
            $lsa = $_POST['lsa'];
            $lysc = $_POST['lysc'];
            $school_id = $_POST['school_id'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_twelve SET 
                sy = ?, lrn = ?, course = ?, lname = ?, fname = ?, mi = ?, bdate = ?, 
                sex = ?, age = ?, contact = ?, email = ?, current_address = ?, perm_address = ?, 
                ffname = ?, flname = ?, fmi = ?, contact_f = ?, mlname = ?, 
                mfname = ?, mmi = ?, contact_m = ?, lglc = ?, lsa = ?, 
                lysc = ?, school_id = ? 
                WHERE id_twelve = ?");
                
            $stmt->execute([
                $sy, $lrn, $course, $lname, $fname, $mi, $bdate, $sex, $age, $contact, $email, 
                $current_address, $perm_address, $ffname, $flname, $fmi, 
                $contact_f, $mlname, $mfname, $mmi, $contact_m, $lglc, 
                $lsa, $lysc, $school_id, 
                $id_twelve // Corrected: Used $id_nine instead of $id_eight
            ]);
            
            echo "<script type='text/javascript'>alert('Grade 12 Data Updated'); window.location.href='grade12.php';</script>";
            exit();
        }
    }
    
    // ─────────────────────────────────────────────────────────────────
    //  GRADE PROMOTION FEATURE
    //  Tables: tbl_seven(7) → tbl_eight(8) → tbl_nine(9) → tbl_ten(10)
    //          → tbl_eleven(11) → tbl_twelve(12)
    //  Grade 7 & 8 have NO 'course' column.
    //  Grades 9-12 HAVE a 'course' column.
    // ─────────────────────────────────────────────────────────────────

    private $grade_table_map = [
        '7'  => ['table' => 'tbl_seven',  'pk' => 'id_seven',  'has_course' => false],
        '8'  => ['table' => 'tbl_eight',  'pk' => 'id_eight',  'has_course' => false],
        '9'  => ['table' => 'tbl_nine',   'pk' => 'id_nine',   'has_course' => true],
        '10' => ['table' => 'tbl_ten',    'pk' => 'id_ten',    'has_course' => true],
        '11' => ['table' => 'tbl_eleven', 'pk' => 'id_eleven', 'has_course' => true],
        '12' => ['table' => 'tbl_twelve', 'pk' => 'id_twelve', 'has_course' => true],
    ];

    /**
     * Returns all active (non-archived) students in a given grade table.
     * Adds a 'record_id' alias so the view can reference the PK generically.
     */
    public function get_students_for_promotion($from_grade) {
        if (!isset($this->grade_table_map[$from_grade])) return [];

        $cfg   = $this->grade_table_map[$from_grade];
        $table = $cfg['table'];
        $pk    = $cfg['pk'];

        $conn  = $this->openConn();
        $sql   = "SELECT *, {$pk} AS record_id FROM {$table}
                  WHERE is_archived = 0 OR is_archived IS NULL
                  ORDER BY lname, fname";
        $stmt  = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Promotes selected students from one grade table to the next.
     *
     * @param  string $from_grade    Source grade number string ('7'–'11')
     * @param  string $to_grade      Target grade number string ('8'–'12')
     * @param  string $new_sy        New school year, e.g. '2026-2027'
     * @param  array  $selected_ids  Array of source PKs to promote
     * @return array  ['success' => bool, 'message' => string]
     */
    public function promote_students($from_grade, $to_grade, $new_sy, $selected_ids) {
        // Validate parameters
        if (!isset($this->grade_table_map[$from_grade]) ||
            !isset($this->grade_table_map[$to_grade])) {
            return ['success' => false, 'message' => 'Invalid grade selection.'];
        }

        $expected_next = (string)((int)$from_grade + 1);
        if ($to_grade !== $expected_next) {
            return ['success' => false, 'message' => 'You can only promote to the immediately next grade.'];
        }

        if (empty($selected_ids)) {
            return ['success' => false, 'message' => 'No students were selected for promotion.'];
        }

        // Sanitise school year format  YYYY-YYYY
        if (!preg_match('/^\d{4}-\d{4}$/', $new_sy)) {
            return ['success' => false, 'message' => 'Invalid school year format. Use YYYY-YYYY (e.g. 2026-2027).'];
        }

        $src_cfg   = $this->grade_table_map[$from_grade];
        $dst_cfg   = $this->grade_table_map[$to_grade];
        $src_table = $src_cfg['table'];
        $src_pk    = $src_cfg['pk'];
        $dst_table = $dst_cfg['table'];
        $dst_has_course = $dst_cfg['has_course'];

        // Course overrides submitted from the form (keyed by source PK)
        $course_overrides = $_POST['course_override'] ?? [];
        $default_course   = trim($_POST['default_course'] ?? '');

        $conn = $this->openConn();
        $promoted = 0;
        $skipped  = 0;
        $errors   = [];

        $conn->beginTransaction();
        try {
            foreach ($selected_ids as $src_id) {
                $src_id = (int)$src_id;

                // Fetch source row
                $fetch = $conn->prepare("SELECT * FROM {$src_table} WHERE {$src_pk} = ?");
                $fetch->execute([$src_id]);
                $row = $fetch->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    $skipped++;
                    continue;
                }

                // Check if already promoted (same resident in destination)
                $dup_check = $conn->prepare(
                    "SELECT COUNT(*) FROM {$dst_table}
                     WHERE id_resident = ? AND (is_archived = 0 OR is_archived IS NULL)"
                );
                $dup_check->execute([$row['id_resident']]);
                if ($dup_check->fetchColumn() > 0) {
                    $skipped++;
                    $errors[] = "Student {$row['lname']}, {$row['fname']} already exists in the destination grade (skipped).";
                    continue;
                }

                // Resolve course value for destination
                $course_val = '';
                if ($dst_has_course) {
                    if (isset($course_overrides[$src_id]) && trim($course_overrides[$src_id]) !== '') {
                        $course_val = trim($course_overrides[$src_id]);
                    } elseif ($default_course !== '') {
                        $course_val = $default_course;
                    } elseif (isset($row['course'])) {
                        $course_val = $row['course'];
                    }
                    if ($course_val === '') $course_val = 'N/A';
                }

                // Build INSERT
                $common_fields = [
                    'id_resident', 'lrn', 'lname', 'fname', 'mi',
                    'bdate', 'sex', 'age', 'contact', 'email',
                    'current_address', 'perm_address',
                    'ffname', 'flname', 'fmi', 'contact_f',
                    'mlname', 'mfname', 'mmi', 'contact_m',
                    'lglc', 'lsa', 'lysc', 'school_id'
                ];

                if ($dst_has_course) {
                    $insert_cols = array_merge(['sy', 'lrn', 'course'], array_diff($common_fields, ['lrn']));
                    // build ordered value list
                    $vals = [
                        $new_sy,
                        $row['lrn'],
                        $course_val,
                        $row['id_resident'],
                        // remaining common fields minus id_resident and lrn
                    ];
                    // Easier: build col=>val map
                    $col_val = ['sy' => $new_sy, 'course' => $course_val];
                    foreach ($common_fields as $f) {
                        $col_val[$f] = $row[$f] ?? '';
                    }
                    // Promoted students are pre-approved — no second approval needed
                    $col_val['enrollment_status'] = 'Approved';
                } else {
                    $col_val = ['sy' => $new_sy];
                    foreach ($common_fields as $f) {
                        $col_val[$f] = $row[$f] ?? '';
                    }
                    // Promoted students are pre-approved — no second approval needed
                    $col_val['enrollment_status'] = 'Approved';
                }

                $cols        = implode(', ', array_keys($col_val));
                $placeholders = implode(', ', array_fill(0, count($col_val), '?'));
                $insert = $conn->prepare(
                    "INSERT INTO {$dst_table} ({$cols}) VALUES ({$placeholders})"
                );
                $insert->execute(array_values($col_val));

                // Archive the source record
                $archive = $conn->prepare(
                    "UPDATE {$src_table} SET is_archived = 1, archived_at = NOW() WHERE {$src_pk} = ?"
                );
                $archive->execute([$src_id]);

                $promoted++;
            }

            $conn->commit();

            $msg = "{$promoted} student(s) promoted from Grade {$from_grade} to Grade {$to_grade} for school year {$new_sy}.";
            if ($skipped > 0) {
                $msg .= " {$skipped} skipped (duplicate or not found).";
            }
            if (!empty($errors)) {
                $msg .= ' Notes: ' . implode(' | ', $errors);
            }
            return ['success' => true, 'message' => $msg];

        } catch (Exception $e) {
            $conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    public function submit_promotion_request() {
        if (!isset($_POST['submit_promotion_request'])) return;
 
        $conn = $this->openConn();
 
        // Ensure table exists
        $conn->exec("CREATE TABLE IF NOT EXISTS tbl_promotion_requests (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            id_resident  INT NOT NULL,
            from_grade   VARCHAR(5) NOT NULL,
            to_grade     VARCHAR(5) NOT NULL,
            record_id    INT NOT NULL,
            documents    TEXT,
            notes        TEXT,
            status       VARCHAR(20) NOT NULL DEFAULT 'Pending',
            reject_reason TEXT,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            reviewed_at  DATETIME,
            reviewed_by  INT
        )");
 
        $user = $this->get_userdata();
        if (empty($user)) { return; }
 
        $id_resident = (int)($user['id_resident'] ?? 0);
        $from_grade  = trim($_POST['from_grade'] ?? '');
        $record_id   = (int)($_POST['record_id'] ?? 0);
        $notes       = trim($_POST['notes'] ?? '');
 
        $grade_map = [
            '7' => '8', '8' => '9', '9' => '10',
            '10' => '11', '11' => '12'
        ];
        if (!isset($grade_map[$from_grade]) || $id_resident === 0 || $record_id === 0) {
            $_SESSION['swal'] = ['icon'=>'error','title'=>'Error','text'=>'Invalid promotion request.'];
            header('Location: promotion_request.php'); exit;
        }
        $to_grade = $grade_map[$from_grade];

        // Bug Fix 2: Verify the student's enrollment in the source grade is actually Approved
        $src_table_map = [
            '7'  => ['table' => 'tbl_seven',  'pk' => 'id_seven'],
            '8'  => ['table' => 'tbl_eight',  'pk' => 'id_eight'],
            '9'  => ['table' => 'tbl_nine',   'pk' => 'id_nine'],
            '10' => ['table' => 'tbl_ten',    'pk' => 'id_ten'],
            '11' => ['table' => 'tbl_eleven', 'pk' => 'id_eleven'],
        ];
        $src = $src_table_map[$from_grade];
        $enr_check = $conn->prepare(
            "SELECT enrollment_status FROM {$src['table']}
             WHERE {$src['pk']} = ? AND id_resident = ? AND (is_archived = 0 OR is_archived IS NULL)
             LIMIT 1"
        );
        $enr_check->execute([$record_id, $id_resident]);
        $enr_row = $enr_check->fetch(PDO::FETCH_ASSOC);
        if (!$enr_row || strtolower($enr_row['enrollment_status'] ?? '') !== 'approved') {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Not Yet Approved',
                'text'=>'Your enrollment for this grade must be approved before you can request a promotion.'];
            header('Location: promotion_request.php'); exit;
        }

        // Bug Fix 3: Block if a Pending OR Approved promotion request already exists for this grade
        $dup = $conn->prepare(
            "SELECT id FROM tbl_promotion_requests
             WHERE id_resident = ? AND from_grade = ? AND status IN ('Pending', 'Approved')"
        );
        $dup->execute([$id_resident, $from_grade]);
        $dup_row = $dup->fetch();
        if ($dup_row) {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'Already Processed',
                'text'=>'You already have a pending or approved promotion request for this grade.'];
            header('Location: promotion_request.php'); exit;
        }
 
        // Handle file uploads
        $uploadedPaths = [];
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/promotion_docs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowedTypes = [
                'image/jpeg','image/png','image/gif','image/webp',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $maxSize = 5 * 1024 * 1024; // 5 MB
            foreach ($_FILES['documents']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['documents']['error'][$idx] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['documents']['size'][$idx] > $maxSize) continue;
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($tmpName);
                if (!in_array($mime, $allowedTypes)) continue;
                $origName  = basename($_FILES['documents']['name'][$idx]);
                $safeName  = time() . '_' . $id_resident . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $origName);
                $dest      = $uploadDir . $safeName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $uploadedPaths[] = 'uploads/promotion_docs/' . $safeName;
                }
            }
        }
 
        $docs_json = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;
 
        $ins = $conn->prepare(
            "INSERT INTO tbl_promotion_requests
             (id_resident, from_grade, to_grade, record_id, documents, notes, status, submitted_at)
             VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())"
        );
        $ins->execute([$id_resident, $from_grade, $to_grade, $record_id, $docs_json, $notes]);
 
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Request Submitted!',
            'text'=>'Your promotion request has been submitted. Please wait for admin approval.'];
        header('Location: my_submissions.php'); exit;
    }
 
    /**
     * Returns all promotion requests for the current resident.
     */
    public function get_my_promotion_requests() {
        $conn = $this->openConn();
        try {
            $conn->exec("CREATE TABLE IF NOT EXISTS tbl_promotion_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_resident INT NOT NULL,
                from_grade VARCHAR(5) NOT NULL,
                to_grade VARCHAR(5) NOT NULL,
                record_id INT NOT NULL,
                documents TEXT,
                notes TEXT,
                status VARCHAR(20) NOT NULL DEFAULT 'Pending',
                reject_reason TEXT,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                reviewed_at DATETIME,
                reviewed_by INT
            )");
            $user = $this->get_userdata();
            if (empty($user)) return [];
            $id_resident = (int)($user['id_resident'] ?? 0);
            $stmt = $conn->prepare(
                "SELECT * FROM tbl_promotion_requests WHERE id_resident = ? ORDER BY submitted_at DESC"
            );
            $stmt->execute([$id_resident]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }
 
    /**
     * Admin: returns all promotion requests with student name info.
     */
    public function admin_get_promotion_requests($status_filter = '') {
        $conn = $this->openConn();
        try {
            $conn->exec("CREATE TABLE IF NOT EXISTS tbl_promotion_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_resident INT NOT NULL,
                from_grade VARCHAR(5) NOT NULL,
                to_grade VARCHAR(5) NOT NULL,
                record_id INT NOT NULL,
                documents TEXT,
                notes TEXT,
                status VARCHAR(20) NOT NULL DEFAULT 'Pending',
                reject_reason TEXT,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                reviewed_at DATETIME,
                reviewed_by INT
            )");
            $where = $status_filter ? "WHERE pr.status = ?" : "";
            $sql = "SELECT pr.*, r.fname, r.lname, r.mi
                    FROM tbl_promotion_requests pr
                    LEFT JOIN tbl_resident r ON r.id_resident = pr.id_resident
                    {$where}
                    ORDER BY pr.submitted_at DESC";
            $stmt = $conn->prepare($sql);
            $status_filter ? $stmt->execute([$status_filter]) : $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }
 
    /**
     * Admin: approve a promotion request and actually promote the student.
     */
    public function admin_approve_promotion_request() {
        if (!isset($_POST['approve_promotion_request'])) return;
        $this->validate_admin();
 
        $id     = (int)($_POST['request_id'] ?? 0);
        $new_sy = trim($_POST['new_sy'] ?? '');
        $conn   = $this->openConn();
 
        // Fetch the promotion request
        $stmt = $conn->prepare("SELECT * FROM tbl_promotion_requests WHERE id = ?");
        $stmt->execute([$id]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$req || $req['status'] !== 'Pending') {
            $_SESSION['swal'] = ['icon'=>'error','title'=>'Error','text'=>'Request not found or already processed.'];
            header('Location: admn_promotion_requests.php'); exit;
        }
 
        if (!preg_match('/^\d{4}-\d{4}$/', $new_sy)) {
            $_SESSION['swal'] = ['icon'=>'error','title'=>'Invalid SY','text'=>'School year must be in YYYY-YYYY format.'];
            header('Location: admn_promotion_requests.php'); exit;
        }
 
        // Fetch the resident's email and name from tbl_resident
        $res = $conn->prepare("SELECT fname, lname, email FROM tbl_resident WHERE id_resident = ?");
        $res->execute([$req['id_resident']]);
        $resident = $res->fetch(PDO::FETCH_ASSOC);
        $email = $resident['email'] ?? '';
        $name  = trim(($resident['fname'] ?? '') . ' ' . ($resident['lname'] ?? ''));
        $from_label = 'Grade ' . $req['from_grade'];
        $to_label   = 'Grade ' . $req['to_grade'];
 
        // Perform the actual promotion
        $result = $this->promote_students(
            $req['from_grade'],
            $req['to_grade'],
            $new_sy,
            [$req['record_id']]
        );
 
        if ($result['success']) {
            $conn->prepare(
                "UPDATE tbl_promotion_requests SET status='Approved', reviewed_at=NOW() WHERE id=?"
            )->execute([$id]);
 
            // Send approval email
            if (!empty($email)) {
                $html = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                    <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                        <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                        <p style='color:#a8c4e0;margin:4px 0 0;'>Grade Promotion Notification</p>
                    </div>
                    <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                        <h3 style='color:#0b2b5c;'>&#127881; Promotion Approved!</h3>
                        <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>We are pleased to inform you that your grade promotion request from
                           <strong>{$from_label}</strong> to <strong>{$to_label}</strong> has been
                           <span style='color:#28a745;font-weight:bold;'>APPROVED</span>
                           for School Year <strong>{$new_sy}</strong>.</p>
                        <p>Please visit the school to complete your enrollment requirements for {$to_label} and for further instructions.</p>
                        <br>
                        <p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
                    </div>
                </div>";
 
                $alt = "Dear {$name},\n\nYour promotion request from {$from_label} to {$to_label} has been APPROVED for School Year {$new_sy}.\nPlease visit the school to complete your requirements.\n\nEusebia Paz Arroyo Memorial National High School";
 
                $mailResult = $this->sendMail($email, $name, "{$to_label} Promotion Approved — Eusebia High School", $html, $alt);
 
                if ($mailResult['success']) {
                    $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved & Promoted!',
                        'text' => $result['message'] . ' Email notification sent to ' . $email . '.'];
                } else {
                    $_SESSION['swal'] = ['icon'=>'warning','title'=>'Approved (Email Failed)',
                        'text' => $result['message'] . ' However, the email could not be sent. ' . ($mailResult['error'] ?? '')];
                }
            } else {
                $_SESSION['swal'] = ['icon'=>'success','title'=>'Approved & Promoted!',
                    'text' => $result['message'] . ' No email address on record.'];
            }
        } else {
            $_SESSION['swal'] = ['icon'=>'error','title'=>'Promotion Failed','text'=>$result['message']];
        }
        header('Location: admn_promotion_requests.php'); exit;
    }
 
    /**
     * Admin: reject a promotion request and email the resident.
     */
    public function admin_reject_promotion_request() {
        if (!isset($_POST['reject_promotion_request'])) return;
        $this->validate_admin();
 
        $id     = (int)($_POST['request_id'] ?? 0);
        $reason = trim($_POST['reject_reason'] ?? '');
        $conn   = $this->openConn();
 
        // Fetch the promotion request
        $stmt = $conn->prepare("SELECT * FROM tbl_promotion_requests WHERE id = ?");
        $stmt->execute([$id]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);
 
        // Fetch the resident's email and name
        $email = '';
        $name  = '';
        $from_label = 'Grade ' . ($req['from_grade'] ?? '');
        $to_label   = 'Grade ' . ($req['to_grade'] ?? '');
        if ($req) {
            $res = $conn->prepare("SELECT fname, lname, email FROM tbl_resident WHERE id_resident = ?");
            $res->execute([$req['id_resident']]);
            $resident = $res->fetch(PDO::FETCH_ASSOC);
            $email = $resident['email'] ?? '';
            $name  = trim(($resident['fname'] ?? '') . ' ' . ($resident['lname'] ?? ''));
        }
 
        $conn->prepare(
            "UPDATE tbl_promotion_requests SET status='Rejected', reject_reason=?, reviewed_at=NOW() WHERE id=?"
        )->execute([$reason, $id]);
 
        // Send rejection email
        if (!empty($email)) {
            $reasonHtml = !empty($reason)
                ? "<p><strong>Reason:</strong> " . htmlspecialchars($reason) . "</p>"
                : "";
            $reasonAlt = !empty($reason) ? "\nReason: {$reason}\n" : "";
 
            $html = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#0b2b5c;padding:24px;border-radius:8px 8px 0 0;text-align:center;'>
                    <h2 style='color:#fff;margin:0;'>Eusebia Paz Arroyo Memorial National High School</h2>
                    <p style='color:#a8c4e0;margin:4px 0 0;'>Grade Promotion Notification</p>
                </div>
                <div style='background:#f9f9f9;padding:30px;border:1px solid #ddd;border-radius:0 0 8px 8px;'>
                    <h3 style='color:#c0392b;'>Promotion Request Not Approved</h3>
                    <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>We regret to inform you that your grade promotion request from
                       <strong>{$from_label}</strong> to <strong>{$to_label}</strong> has been
                       <span style='color:#c0392b;font-weight:bold;'>REJECTED</span>.</p>
                    {$reasonHtml}
                    <p>If you have questions or would like to appeal, please visit the school during office hours and bring the necessary documents.</p>
                    <br>
                    <p style='color:#888;font-size:12px;'>This is an automated message. Please do not reply.</p>
                </div>
            </div>";
 
            $alt = "Dear {$name},\n\nYour promotion request from {$from_label} to {$to_label} has been REJECTED.{$reasonAlt}\nPlease visit the school if you have questions.\n\nEusebia Paz Arroyo Memorial National High School";
 
            $mailResult = $this->sendMail($email, $name, "{$to_label} Promotion Request Rejected — Eusebia High School", $html, $alt);
 
            if ($mailResult['success']) {
                $_SESSION['swal'] = ['icon'=>'info','title'=>'Request Rejected',
                    'text' => 'The promotion request has been rejected and a notification email was sent to ' . $email . '.'];
            } else {
                $_SESSION['swal'] = ['icon'=>'warning','title'=>'Rejected (Email Failed)',
                    'text' => 'Request rejected but email could not be sent. ' . ($mailResult['error'] ?? '')];
            }
        } else {
            $_SESSION['swal'] = ['icon'=>'info','title'=>'Request Rejected',
                'text' => 'The promotion request has been rejected. No email address on record.'];
        }
        header('Location: admn_promotion_requests.php'); exit;
    }

    public function admin_bulk_delete_promotion_requests() {
        if (!isset($_POST['bulk_delete_promotion'])) return;
        $this->validate_admin();

        $ids = $_POST['selected_ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['swal'] = ['icon'=>'warning','title'=>'No Selection','text'=>'Please select at least one request to delete.'];
            header('Location: admn_promotion_requests.php'); exit;
        }

        $conn = $this->openConn();
        $ids  = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $conn->prepare("DELETE FROM tbl_promotion_requests WHERE id IN ({$placeholders})")->execute($ids);

        $count = count($ids);
        $_SESSION['swal'] = ['icon'=>'success','title'=>'Deleted','text'=>"{$count} promotion request(s) deleted."];
        header('Location: admn_promotion_requests.php'); exit;
    }

}
$eusebia = new EUSEBIAClass(); //variable to call outside of its class

?>