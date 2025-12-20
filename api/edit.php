<?php
/**
 * API Edit - Modifica ed Elimina Flashcard (AGGIORNATO)
 * 
 * MODIFICHE:
 * - Supporto multi-utente tramite sessione
 * - Supporto immagini caricate localmente (image_path)
 * - Elimina l'immagine locale quando la carta viene eliminata
 * 
 * Endpoint per:
 * - GET: Recuperare una singola carta da modificare
 * - POST: Aggiornare una carta esistente
 * - DELETE: Eliminare una carta
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();
$userId = getCurrentUserId();

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
    
    $stmt = $db->prepare('SELECT * FROM flashcards WHERE id = ? AND user_id = ?');
    $stmt->execute([$cardId, $userId]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($card) {
        // Aggiungi effective_image
        if (!empty($card['image_path'])) {
            $card['effective_image'] = '/' . $card['image_path'];
        } elseif (!empty($card['image_url'])) {
            $card['effective_image'] = $card['image_url'];
        } else {
            $card['effective_image'] = null;
        }
        
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
    $imagePath = isset($data['image_path']) ? trim($data['image_path']) : null;
    
    // Se image_url è vuota, salvala come NULL
    if (empty($imageUrl)) $imageUrl = null;
    if (empty($imagePath)) $imagePath = null;
    
    // Validazione URL immagine (se presente)
    if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL immagine non valido']);
        exit;
    }
    
    // Recupera la carta esistente per gestire l'immagine precedente
    $stmt = $db->prepare('SELECT image_path FROM flashcards WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    $existingCard = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingCard) {
        http_response_code(404);
        echo json_encode(['error' => 'Carta non trovata']);
        exit;
    }
    
    // Se c'era un'immagine locale e viene sostituita, elimina la vecchia
    $oldImagePath = $existingCard['image_path'];
    if ($oldImagePath && $oldImagePath !== $imagePath) {
        $fullOldPath = __DIR__ . '/../' . $oldImagePath;
        if (file_exists($fullOldPath)) {
            @unlink($fullOldPath);
        }
    }
    
    try {
        $stmt = $db->prepare('
            UPDATE flashcards 
            SET question = ?, answer = ?, category = ?, image_url = ?, image_path = ?
            WHERE id = ? AND user_id = ?
        ');
        $stmt->execute([$question, $answer, $category, $imageUrl, $imagePath, $id, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Carta aggiornata con successo'
            ]);
        } else {
            // Potrebbe essere che i dati non sono cambiati
            echo json_encode([
                'success' => true,
                'message' => 'Nessuna modifica effettuata'
            ]);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore database: ' . $e->getMessage()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    /**
     * DELETE: Elimina una carta
     * 
     * NOTA: Elimina anche l'immagine locale associata per non
     * lasciare file orfani sul disco.
     */
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? (int)$data['id'] : $cardId;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID carta mancante']);
        exit;
    }
    
    // Prima recupera la carta per eliminare l'eventuale immagine
    $stmt = $db->prepare('SELECT image_path FROM flashcards WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        http_response_code(404);
        echo json_encode(['error' => 'Carta non trovata']);
        exit;
    }
    
    try {
        // Elimina la carta dal database
        $stmt = $db->prepare('DELETE FROM flashcards WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        
        if ($stmt->rowCount() > 0) {
            // Elimina l'immagine locale se esiste
            if (!empty($card['image_path'])) {
                $fullPath = __DIR__ . '/../' . $card['image_path'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            
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
