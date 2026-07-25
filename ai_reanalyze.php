<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
    header('Content-Type: application/json');

    require('classes/student.class.php');
    $eusebia->validate_admin();

    $tables = [
        'seven'  => ['table' => 'tbl_seven',  'id' => 'id_seven'],
        'eight'  => ['table' => 'tbl_eight',  'id' => 'id_eight'],
        'nine'   => ['table' => 'tbl_nine',   'id' => 'id_nine'],
        'ten'    => ['table' => 'tbl_ten',    'id' => 'id_ten'],
        'eleven' => ['table' => 'tbl_eleven', 'id' => 'id_eleven'],
        'twelve' => ['table' => 'tbl_twelve', 'id' => 'id_twelve'],
    ];

    $grade = $_POST['grade'] ?? '';
    $id    = (int)($_POST['id'] ?? 0);

    if (!isset($tables[$grade]) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    $table    = $tables[$grade]['table'];
    $idColumn = $tables[$grade]['id'];

    $connection = $eusebia->openConn();
    $stmt = $connection->prepare("SELECT * FROM `{$table}` WHERE `{$idColumn}` = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Record not found.']);
        exit;
    }

    $documentPaths = json_decode($row['documents'] ?? '[]', true) ?: [];

    $formData = [
        'Full Name'                  => trim(($row['lname'] ?? '') . ', ' . ($row['fname'] ?? '') . ' ' . ($row['mi'] ?? '')),
        'Birthdate'                  => $row['bdate'] ?? '',
        'LRN'                        => $row['lrn'] ?? '',
        'Last School Attended'       => $row['lsa'] ?? '',
        'Last Grade Level Completed' => $row['lglc'] ?? '',
        'Is 4Ps Beneficiary'         => $row['is_4ps'] ?? '',
        '4Ps Household ID'           => $row['fourps_id'] ?? '',
        'Is IP Member'               => $row['is_ip'] ?? '',
        'IP Group'                   => $row['ip_group'] ?? '',
    ];
    if (isset($row['course'])) {
        $formData['Course/Strand'] = $row['course'];
    }

    // run_ai_document_review() opens its own connection and writes the result
    // straight to the row's ai_analysis column.
    $eusebia->run_ai_document_review($table, $idColumn, $id, $documentPaths, $formData);

    // Read back what was just stored so the admin page can refresh in place.
    $stmt2 = $connection->prepare("SELECT ai_analysis FROM `{$table}` WHERE `{$idColumn}` = ?");
    $stmt2->execute([$id]);
    $result = $stmt2->fetch();

    echo json_encode([
        'success'     => true,
        'ai_analysis' => $result['ai_analysis'] ?? null,
    ]);
