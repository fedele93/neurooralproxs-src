<?php
/**
 * API Cards - Gestione Flashcard
 * 
 * Endpoint per creare e recuperare flashcard.
 * Supporta:
 * - Creazione carte con categoria e immagine
 * - Recupero tutte le carte dell'utente
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * CREAZIONE NUOVA CARTA
     * 
     * Parametri richiesti: question, answer
     * Parametri opzionali: category, image_url
     */
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validazione input
    if (empty($data['question']) || empty($data['answer'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Domanda e risposta sono obbligatorie']);
        exit;
    }
    
    $question = trim($data['question']);
    $answer = trim($data['answer']);
    $category = isset($data['category']) ? trim($data['category']) : 'Generale';
    $imageUrl = isset($data['image_url']) ? trim($data['image_url']) : null;
    
    // Validazione URL immagine (se presente)
    if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL immagine non valido']);
        exit;
    }
    
    try {
        $stmt = $db->prepare('
            INSERT INTO flashcards 
            (question, answer, category, image_url, user_id, easiness_factor, interval, repetitions, next_review) 
            VALUES (?, ?, ?, ?, 1, 2.5, 0, 0, date("now"))
        ');
        $stmt->execute([$question, $answer, $category, $imageUrl]);
        
        echo json_encode([
            'success' => true, 
            'id' => $db->lastInsertId(),
            'message' => 'Carta creata con successo'
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    /**
     * RECUPERO CARTE
     * 
     * Restituisce tutte le carte dell'utente con statistiche aggregate
     */
    try {
        $stmt = $db->query('
            SELECT 
                id, question, answer, category, image_url,
                easiness_factor, interval, repetitions, next_review,
                COALESCE(lapses, 0) as lapses,
                last_review
            FROM flashcards 
            WHERE user_id = 1
            ORDER BY category, id
        ');
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'cards' => $cards,
            'total' => count($cards)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
}
