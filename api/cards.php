<?php
/**
 * API Cards - Gestione Flashcard (AGGIORNATO)
 * 
 * MODIFICHE:
 * - Supporto multi-utente tramite sessione
 * - Supporto immagini caricate localmente (image_path)
 * - Mantiene compatibilità con URL esterni (image_url)
 * 
 * Endpoint per creare e recuperare flashcard.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();
$userId = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * CREAZIONE NUOVA CARTA
     * 
     * Parametri richiesti: question, answer
     * Parametri opzionali: category, image_url, image_path
     * 
     * NOTA SU IMMAGINI:
     * - image_url: URL esterno (es. https://example.com/image.jpg)
     * - image_path: Percorso locale dopo upload (es. uploads/user_1/abc123.jpg)
     * Puoi usare uno dei due, o nessuno. image_path ha priorità.
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
    $imagePath = isset($data['image_path']) ? trim($data['image_path']) : null;
    
    // Validazione URL immagine (se presente e non vuoto)
    if ($imageUrl && !empty($imageUrl) && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL immagine non valido']);
        exit;
    }
    
    // Pulisci valori vuoti
    if (empty($imageUrl)) $imageUrl = null;
    if (empty($imagePath)) $imagePath = null;
    
    try {
        $stmt = $db->prepare('
            INSERT INTO flashcards 
            (question, answer, category, image_url, image_path, user_id, easiness_factor, interval, repetitions, next_review) 
            VALUES (?, ?, ?, ?, ?, ?, 2.5, 0, 0, date("now"))
        ');
        $stmt->execute([$question, $answer, $category, $imageUrl, $imagePath, $userId]);
        
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
     * Restituisce tutte le carte dell'utente corrente con statistiche aggregate.
     * Include sia image_url che image_path per flessibilità.
     */
    try {
        $stmt = $db->prepare('
            SELECT 
                id, question, answer, category, 
                image_url, image_path,
                easiness_factor, interval, repetitions, next_review,
                COALESCE(lapses, 0) as lapses,
                COALESCE(suspended, 0) as suspended,
                last_review
            FROM flashcards 
            WHERE user_id = ?
            ORDER BY category, id
        ');
        $stmt->execute([$userId]);
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Aggiungi campo 'effective_image' che restituisce l'immagine da usare
        // Priorità: image_path > image_url
        foreach ($cards as &$card) {
            if (!empty($card['image_path'])) {
                $card['effective_image'] = '/' . $card['image_path'];
            } elseif (!empty($card['image_url'])) {
                $card['effective_image'] = $card['image_url'];
            } else {
                $card['effective_image'] = null;
            }
        }
        
        echo json_encode([
            'cards' => $cards,
            'total' => count($cards),
            'user_id' => $userId
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
}
