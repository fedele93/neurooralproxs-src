<?php
header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $category = $data['category'] ?? 'Generale';
    
    $stmt = $db->prepare('INSERT INTO flashcards (question, answer, category, user_id) VALUES (?, ?, ?, 1)');
    $stmt->execute([$data['question'], $data['answer'], $category]);
    
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
} else {
    $stmt = $db->query('SELECT * FROM flashcards WHERE user_id = 1');
    echo json_encode(['cards' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
