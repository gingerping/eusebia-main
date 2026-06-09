<?php
// AJAX: look up existing student data by LRN across all grade tables
header('Content-Type: application/json');
error_reporting(0);
require 'classes/conn.php';

$lrn = trim($_GET['lrn'] ?? '');
if (strlen($lrn) < 3) { echo json_encode(['found' => false]); exit; }

// Map table name => its primary key column
$tables = [
    'tbl_seven'  => 'id_seven',
    'tbl_eight'  => 'id_eight',
    'tbl_nine'   => 'id_nine',
    'tbl_ten'    => 'id_ten',
    'tbl_eleven' => 'id_eleven',
    'tbl_twelve' => 'id_twelve',
];

$fields = 'lrn, lname, fname, mi, bdate, sex, age, contact, email,
           current_address, perm_address,
           ffname, flname, fmi, contact_f,
           mlname, mfname, mmi, contact_m,
           lglc, lsa, lysc, school_id';

foreach ($tables as $tbl => $pk) {
    try {
        // Simple query — no ORDER BY on columns that may not exist yet
        $stmt = $conn->prepare(
            "SELECT {$pk}, {$fields} FROM `{$tbl}`
             WHERE `lrn` = ?
             AND (is_archived = 0 OR is_archived IS NULL)
             LIMIT 1"
        );
        $stmt->execute([$lrn]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $source_id = $row[$pk];
            unset($row[$pk]);
            echo json_encode([
                'found'        => true,
                'data'         => $row,
                'source_table' => $tbl,
                'source_id'    => $source_id,
            ]);
            exit;
        }
    } catch (Exception $e) {
        // Log the actual error for debugging
        error_log("lookup_lrn.php error on $tbl: " . $e->getMessage());
    }
}

echo json_encode(['found' => false]);
$conn = null;