<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    header('Content-Type: application/json');

    require('classes/resident.class.php');
    $eusebia->validate_admin();

    $input = json_decode(file_get_contents('php://input'), true);
    $ids   = $input['ids'] ?? [];

    // Validate: must be non-empty array of integers
    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['success' => false, 'message' => 'No IDs provided.']);
        exit;
    }

    // Sanitize: keep only integers
    $ids = array_filter(array_map('intval', $ids), fn($id) => $id > 0);

    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'Invalid IDs.']);
        exit;
    }

    try {
        $connection  = $eusebia->openConn();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $connection->prepare("DELETE FROM tbl_resident WHERE id_resident IN ($placeholders)");
        $stmt->execute(array_values($ids));
        $deleted = $stmt->rowCount();

        echo json_encode(['success' => true, 'deleted' => $deleted]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }