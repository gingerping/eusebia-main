<?php
// AJAX: check if LRN is already taken
// For old/transferee students moving to grade 8:
//   - Their LRN exists in a previous grade table — that's fine
//   - Block only if they're already enrolled in tbl_eight
header('Content-Type: application/json');
error_reporting(0);
require 'classes/conn.php';

$lrn          = trim($_GET['lrn'] ?? '');
$student_type = trim($_GET['student_type'] ?? 'new');   // new | old | transferee
$source_table = trim($_GET['source_table'] ?? '');      // table their LRN came from (e.g. tbl_seven)

if ($lrn === '') {
    echo json_encode(['taken' => false]);
    exit;
}

$all_tables = ['tbl_seven','tbl_eight','tbl_nine','tbl_ten','tbl_eleven','tbl_twelve'];

if ($student_type === 'old' || $student_type === 'transferee') {
    // Only check tbl_eight — old students are ALLOWED to have their LRN in a previous grade table
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `tbl_eight` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
    $stmt->execute([$lrn]);
    $taken = $stmt->fetchColumn() > 0;
    echo json_encode(['taken' => $taken]);
    exit;
}

// New student: LRN must not exist in any table
$taken = false;
foreach ($all_tables as $tbl) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `{$tbl}` WHERE `lrn` = ? AND (is_archived = 0 OR is_archived IS NULL)");
    $stmt->execute([$lrn]);
    if ($stmt->fetchColumn() > 0) { $taken = true; break; }
}

echo json_encode(['taken' => $taken]);
$conn = null;