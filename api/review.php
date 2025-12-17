<?php
header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Algoritmo SM-2 semplificato
    $stmt = $db->prepare('SELECT * FROM flashcards WHERE id = ?');
    $stmt->execute([$data['card_id']]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        echo json_encode(['error' => 'Card not found']);
        exit;
    }
    
    $quality = $data['quality'];
    $ef = max(1.3, $card['easiness_factor'] + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)));
    
    if ($quality < 3) {
        $interval = 0;
        $reps = 0;
    } else {
        $reps = $card['repetitions'] + 1;
        if ($reps == 1) {
            $interval = 1;
        } elseif ($reps == 2) {
            $interval = 6;
        } else {
            $interval = round($card['interval'] * $ef);
        }
    }
    
    $next_review = date('Y-m-d', strtotime("+{$interval} days"));
    
    $stmt = $db->prepare('UPDATE flashcards SET easiness_factor = ?, interval = ?, repetitions = ?, next_review = ? WHERE id = ?');
    $stmt->execute([$ef, $interval, $reps, $next_review, $data['card_id']]);
    
    echo json_encode(['success' => true]);
} else {
    $stmt = $db->query("SELECT * FROM flashcards WHERE user_id = 1 AND next_review <= date('now') ORDER BY RANDOM() LIMIT 1");
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['card' => $card ?: null]);
}
