<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$messageId = $data['messageId'] ?? null;
$content = $data['content'] ?? '';
$posX = $data['posX'] ?? 0;
$posY = $data['posY'] ?? 0;

if (!$messageId || $content === '') {
    echo json_encode(['status' => 'error', 'message' => 'Празна бележка не може да бъде запазена!']);
    exit;
}

try {
    $db = new DB();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO notes (content, posX, posY) VALUES (?, ?, ?)");
    $stmt->execute([$content, $posX, $posY]);
    $noteId = $conn->lastInsertId();   //important!

    $stmt2 = $conn->prepare("INSERT INTO message_note (messageId, noteId) VALUES (?, ?)");
    $stmt2->execute([$messageId, $noteId]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}