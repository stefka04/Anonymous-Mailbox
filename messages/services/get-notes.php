<?php
require_once __DIR__ . '/../../db/db.php';

header('Content-Type: application/json');

session_start();
/*if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => ['Методът не е разрешен.']]);
    exit;
}*/

$messageId = $_SESSION['message']['id'] ?? null;

if (!$messageId) {
    echo json_encode(['status' => 'error', 'message' => ['Липсва messageId.']]);
    exit;
}

try {
    $db = new DB();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT notes.id, notes.content, notes.posX, notes.posY
        FROM notes
        JOIN message_note ON notes.id = message_note.noteId
        WHERE message_note.messageId = :messageId
    ");

    $stmt->execute(['messageId' => $messageId]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $notes]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => ['Грешка при извличане на бележките.']]);
}