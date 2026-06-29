<?php
// TEMPORARY DEBUG FILE - delete after testing
session_start();
require('classes/main.class.php');

$grade  = isset($_GET['grade'])  ? (int)$_GET['grade'] : 0;
$strand = isset($_GET['strand']) ? trim($_GET['strand']) : '';

$grade_config = [
    7  => ['table'=>'tbl_seven',  'has_course'=>false, 'has_status'=>true],
    8  => ['table'=>'tbl_eight',  'has_course'=>false, 'has_status'=>true],
    9  => ['table'=>'tbl_nine',   'has_course'=>true,  'has_status'=>true],
    10 => ['table'=>'tbl_ten',    'has_course'=>true,  'has_status'=>false],
    11 => ['table'=>'tbl_eleven', 'has_course'=>true,  'has_status'=>false],
    12 => ['table'=>'tbl_twelve', 'has_course'=>true,  'has_status'=>true],
];

$cfg   = $grade_config[$grade] ?? null;
$table = $cfg ? $cfg['table'] : 'UNKNOWN';

$conn   = $eusebia->openConn();
$where  = ["(is_archived=0 OR is_archived IS NULL)"];
$params = [];

if ($cfg && $cfg['has_status'])                          $where[] = "enrollment_status='Approved'";
if ($cfg && $cfg['has_course'] && in_array($grade,[11,12]) && $strand !== '') {
    $where[] = "course=?"; $params[] = $strand;
}

$sql = "SELECT COUNT(*) FROM `{$table}` WHERE " . implode(' AND ', $where);

try {
    $s = $conn->prepare($sql);
    $s->execute($params);
    $count = $s->fetchColumn();
} catch (Exception $e) {
    $count = 'ERROR: ' . $e->getMessage();
}

echo "<pre style='font-size:14px;padding:20px;'>";
echo "GET grade   = " . var_export($grade, true) . "\n";
echo "GET strand  = " . var_export($strand, true) . "\n";
echo "Table       = " . $table . "\n";
echo "SQL         = " . $sql . "\n";
echo "Params      = " . var_export($params, true) . "\n";
echo "Row count   = " . $count . "\n";
echo "</pre>";
?>