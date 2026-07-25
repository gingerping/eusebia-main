<?php
/**
 * AJAX endpoint: marks the logged-in student's notifications as read.
 * Called from student_navbar.php when the notification bell is opened.
 */
error_reporting(E_ALL ^ E_WARNING);
require('classes/student.class.php');
header('Content-Type: application/json');

$userdetails = $eusebia->get_userdata();
$id_student  = $userdetails['id_student'] ?? null;

if ($id_student) {
    $eusebia->mark_all_notifications_read($id_student);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
}
