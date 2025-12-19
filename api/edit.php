<?php
/**
 * API Edit - Modifica ed Elimina Flashcard
 * 
 * Endpoint per:
 * - GET: Recuperare una singola carta da modificare
 * - POST: Aggiornare una carta esistente
 * - DELETE: Eliminare una carta
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// Leggi l'ID dalla query string o dal body
$cardId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    /**
     * GET: Recupera una carta specifica per modificarla
     */
    if (!$cardId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID carta mancante']);
        exit;
    }
    
    $stmt = $db->prepare('SELECT * FROM flashcards WHERE id = ? AND user_id = 1');
    $stmt->execute([$cardId]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($card) {
        echo json_encode(['success' => true, 'card' => $card]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Carta non trovata']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * POST: Aggiorna una carta esistente
     */
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID carta mancante']);
        exit;
    }
    
    // Validazione
    if (empty($data['question']) || empty($data['answer'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Domanda e risposta sono obbligatorie']);
        exit;
    }
    
    $id = (int)$data['id'];
    $question = trim($data['question']);
    $answer = trim($data['answer']);
    $category = isset($data['category']) ? trim($data['category']) : 'Generale';
    $imageUrl = isset($data['image_url']) ? trim($data['image_url']) : null;
    
    // Se image_url è vuota, salvala come NULL
    if (empty($imageUrl)) {
        $imageUrl = null;
    }
    
    // Validazione URL immagine (se presente)
    if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL immagine non valido']);
        exit;
    }
    
    try {
        $stmt = $db->prepare('
            UPDATE flashcards 
            SET question = ?, answer = ?, category = ?, image_url = ?
            WHERE id = ? AND user_id = 1
        ');
        $stmt->execute([$question, $answer, $category, $imageUrl, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Carta aggiornata con successo'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Carta non trovata']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    /**
     * DELETE: Elimina una carta
     */
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? (int)$data['id'] : $cardId;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID carta mancante']);
        exit;
    }
    
    try {
        $stmt = $db->prepare('DELETE FROM flashcards WHERE id = ? AND user_id = 1');
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Carta eliminata'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Carta non trovata']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
}
