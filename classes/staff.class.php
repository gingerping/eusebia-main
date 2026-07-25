<?php 

    require_once('main.class.php');

    class StaffClass extends EUSEBIAClass {

    // ==================== CRUD ====================

        public function create_staff() {
            if (!isset($_POST['add_staff'])) return;

            $email           = trim($_POST['email'] ?? '');
            $password        = $_POST['password'] ?? '';
            $lname           = trim($_POST['lname'] ?? '');
            $fname           = trim($_POST['fname'] ?? '');
            $mi              = trim($_POST['mi'] ?? '');
            $age             = (int)($_POST['age'] ?? 0);
            $sex             = $_POST['sex'] ?? '';
            $address         = trim($_POST['address'] ?? '');
            $contact         = trim($_POST['contact'] ?? '');
            $position        = trim($_POST['position'] ?? '');
            $addedby         = trim($_POST['addedby'] ?? '');
            $subject_handled = trim($_POST['subject_handled'] ?? '');
            $adviser_grade   = trim($_POST['adviser_grade'] ?? '');
            $subject_grades  = isset($_POST['subject_grades']) && is_array($_POST['subject_grades'])
                               ? implode(',', $_POST['subject_grades']) : '';

            if ($this->check_staff_email($email) > 0) {
                echo "<script>Swal.fire({ icon:'error', title:'Duplicate Email', text:'That email is already registered.' });</script>";
                return;
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_user
                (`email`,`password`,`lname`,`fname`,`mi`,`age`,`sex`,`address`,
                 `contact`,`position`,`role`,`addedby`,`subject_handled`,`adviser_grade`,`subject_grades`)
                VALUES (?,?,?,?,?,?,?,?,?,?,'staff',?,?,?,?)");
            $stmt->execute([$email, $hashed, $lname, $fname, $mi, $age, $sex,
                            $address, $contact, $position, $addedby,
                            $subject_handled, $adviser_grade, $subject_grades]);

            echo "<script>
                Swal.fire({ icon:'success', title:'Teacher Added', text:'Account created successfully.', timer:1800, showConfirmButton:false })
                .then(() => { window.location.reload(); });
            </script>";
        }

        public function update_staff() {
            if (!isset($_POST['update_staff'])) return;

            $id_user         = (int)($_POST['id_user'] ?? 0);
            $lname           = trim($_POST['lname'] ?? '');
            $fname           = trim($_POST['fname'] ?? '');
            $mi              = trim($_POST['mi'] ?? '');
            $age             = (int)($_POST['age'] ?? 0);
            $sex             = $_POST['sex'] ?? '';
            $address         = trim($_POST['address'] ?? '');
            $contact         = trim($_POST['contact'] ?? '');
            $position        = trim($_POST['position'] ?? '');
            $subject_handled = trim($_POST['subject_handled'] ?? '');
            $adviser_grade   = trim($_POST['adviser_grade'] ?? '');
            $subject_grades  = isset($_POST['subject_grades']) && is_array($_POST['subject_grades'])
                               ? implode(',', $_POST['subject_grades']) : '';
            $new_password    = trim($_POST['password'] ?? '');

            $connection = $this->openConn();

            if (!empty($new_password)) {
                $stmt = $connection->prepare("UPDATE tbl_user SET
                    `password`=?, lname=?, fname=?, mi=?, age=?, sex=?, `address`=?,
                    contact=?, position=?, subject_handled=?, adviser_grade=?, subject_grades=?
                    WHERE id_user=?");
                $stmt->execute([
                    password_hash($new_password, PASSWORD_DEFAULT),
                    $lname, $fname, $mi, $age, $sex, $address,
                    $contact, $position, $subject_handled, $adviser_grade, $subject_grades,
                    $id_user
                ]);
            } else {
                $stmt = $connection->prepare("UPDATE tbl_user SET
                    lname=?, fname=?, mi=?, age=?, sex=?, `address`=?,
                    contact=?, position=?, subject_handled=?, adviser_grade=?, subject_grades=?
                    WHERE id_user=?");
                $stmt->execute([
                    $lname, $fname, $mi, $age, $sex, $address,
                    $contact, $position, $subject_handled, $adviser_grade, $subject_grades,
                    $id_user
                ]);
            }

            echo "<script>
                Swal.fire({ icon:'success', title:'Updated', text:'Teacher record updated.', timer:1800, showConfirmButton:false })
                .then(() => { window.location.reload(); });
            </script>";
        }

        public function delete_staff() {
            if (!isset($_POST['delete_staff'])) return;
            $id_user = (int)($_POST['id_user'] ?? 0);
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_user WHERE id_user=?");
            $stmt->execute([$id_user]);
            echo "<script>
                Swal.fire({ icon:'info', title:'Removed', text:'Teacher account deleted.', timer:1500, showConfirmButton:false })
                .then(() => { window.location.reload(); });
            </script>";
        }

    // ==================== PROMOTE STUDENT TO STAFF ====================

        public function promote_student_to_staff() {
            if (!isset($_POST['promote_student'])) return;

            $id_student     = (int)($_POST['id_student'] ?? 0);
            $position        = trim($_POST['position'] ?? '');
            $subject_handled = trim($_POST['subject_handled'] ?? '');
            $adviser_grade   = trim($_POST['adviser_grade'] ?? '');
            $subject_grades  = isset($_POST['subject_grades']) && is_array($_POST['subject_grades'])
                               ? implode(',', $_POST['subject_grades']) : '';

            if (!$id_student || !$position) {
                echo "<script>Swal.fire({ icon:'error', title:'Missing Info', text:'Please select a position.' });</script>";
                return;
            }

            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_student WHERE id_student=? AND is_archived=0");
            $stmt->execute([$id_student]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                echo "<script>Swal.fire({ icon:'error', title:'Not Found', text:'Student account not found.' });</script>";
                return;
            }

            $email   = $student['email'] ?? null;
            $contact = $student['contact'] ?? '';
            $address = trim(implode(', ', array_filter([
                $student['houseno']   ?? '',
                $student['street']    ?? '',
                $student['brgy']      ?? '',
                $student['municipal'] ?? ''
            ])));

            $chk = $connection->prepare("SELECT id_user FROM tbl_user WHERE email=?");
            $chk->execute([$email]);
            if ($chk->rowCount() > 0) {
                echo "<script>Swal.fire({ icon:'warning', title:'Already a Teacher', text:'This account has already been promoted.' });</script>";
                return;
            }

            $stmt2 = $connection->prepare("INSERT INTO tbl_user
                (`email`,`password`,`lname`,`fname`,`mi`,`age`,`sex`,
                 `address`,`contact`,`position`,`role`,`addedby`,
                 `subject_handled`,`adviser_grade`,`subject_grades`)
                VALUES (?,?,?,?,?,?,?,?,?,?,'staff','Admin-Promoted',?,?,?)");
            $stmt2->execute([
                $email, $student['password'],
                $student['lname'], $student['fname'], $student['mi'],
                $student['age'],   $student['sex'],   $address, $contact,
                $position, $subject_handled, $adviser_grade, $subject_grades
            ]);

            $del = $connection->prepare("DELETE FROM tbl_student WHERE id_student=?");
            $del->execute([$id_student]);

            echo "<script>
                Swal.fire({
                    icon: 'success', title: 'Promoted!',
                    html: 'Account is now a Teacher/Staff.<br>Their student account has been removed.<br>They can log in immediately using their existing password.',
                    confirmButtonColor: '#155724'
                }).then(() => { window.location.reload(); });
            </script>";
        }

    // ==================== QUERIES ====================

        public function view_staff() {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE role='staff' ORDER BY lname ASC, fname ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function get_single_staff($id_user) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE id_user=?");
            $stmt->execute([(int)$id_user]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }

        public function check_staff_email($email) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT id_user FROM tbl_user WHERE email=?");
            $stmt->execute([$email]);
            return $stmt->rowCount();
        }

        public function count_staff() {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_user WHERE role='staff'");
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function count_mstaff() {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_user WHERE role='staff' AND sex='Male'");
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function count_fstaff() {
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_user WHERE role='staff' AND sex='Female'");
            $stmt->execute();
            return $stmt->fetchColumn();
        }

    } // end class StaffClass

    $staffbmis    = new StaffClass();
    $staffeusebia = $staffbmis;
?>