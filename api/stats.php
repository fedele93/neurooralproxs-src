<?php
header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

$total = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1")->fetch()['count'];
$due = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND next_review <= date('now')")->fetch()['count'];
$new = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND repetitions = 0")->fetch()['count'];

echo json_encode([
    'total' => $total,
    'due' => $due,
    'new' => $new
]);
