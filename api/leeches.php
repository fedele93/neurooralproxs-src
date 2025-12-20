<?php
/**
 * API Leeches - Gestione Carte Sospese (AGGIORNATO)
 * 
 * MODIFICHE:
 * - Supporto multi-utente tramite sessione
 * 
 * Questo endpoint permette di:
 * - Riattivare una carta leech (dopo averla riformulata)
 * - Eliminare definitivamente una leech
 * 
 * NOTA SCIENTIFICA [rif. 133 - SuperMemo 20 Rules]:
 * Prima di riattivare una leech, dovresti:
 * 1. Dividere la carta in unità più piccole
 * 2. Aggiungere elementi visivi (dual coding)
 * 3. Creare mnemoniche o associazioni
 * 4. Verificare di avere le conoscenze prerequisite
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();
$userId = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $cardId = $data['card_id'] ?? null;
    
    if (!$cardId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID carta mancante']);
        exit;
    }
    
    switch ($action) {
        case 'reactivate':
            /**
             * RIATTIVA UNA LEECH
             * 
             * Quando riattivi una leech:
             * - suspended torna a 0
             * - lapses viene dimezzato (perdono parziale, non reset completo)
             * - EF viene leggermente aumentato (nuova chance)
             * - La carta viene programmata per domani
             */
            $stmt = $db->prepare('
                UPDATE flashcards 
                SET suspended = 0,
                    lapses = MAX(0, lapses / 2),
                    easiness_factor = MIN(2.5, easiness_factor + 0.2),
                    repetitions = 0,
                    interval = 1,
                    next_review = date("now", "+1 day")
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$cardId, $userId]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Carta riattivata! Apparirà domani nel ripasso.'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Carta non trovata']);
            }
            break;
            
        case 'delete':
            /**
             * ELIMINA DEFINITIVAMENTE
             * 
             * Da usare solo se:
             * - Il concetto non è più rilevante
             * - Hai creato carte sostitutive migliori
             * - La carta era un duplicato
             */
            
            // Prima recupera la carta per eliminare l'eventuale immagine locale
            $stmt = $db->prepare('SELECT image_path FROM flashcards WHERE id = ? AND user_id = ?');
            $stmt->execute([$cardId, $userId]);
            $card = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $db->prepare('DELETE FROM flashcards WHERE id = ? AND user_id = ?');
            $stmt->execute([$cardId, $userId]);
            
            if ($stmt->rowCount() > 0) {
                // Elimina l'immagine locale se esiste
                if ($card && !empty($card['image_path'])) {
                    $fullPath = __DIR__ . '/../' . $card['image_path'];
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Carta eliminata definitivamente.'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Carta non trovata']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Azione non valida. Usa: reactivate, delete']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    /**
     * GET: Lista tutte le leeches con dettagli
     */
    $stmt = $db->prepare("
        SELECT id, question, answer, category, lapses, easiness_factor, 
               last_review, next_review, image_url, image_path
        FROM flashcards 
        WHERE user_id = ? AND suspended = 1
        ORDER BY lapses DESC, last_review DESC
    ");
    $stmt->execute([$userId]);
    $leeches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Aggiungi effective_image
    foreach ($leeches as &$leech) {
        if (!empty($leech['image_path'])) {
            $leech['effective_image'] = '/' . $leech['image_path'];
        } elseif (!empty($leech['image_url'])) {
            $leech['effective_image'] = $leech['image_url'];
        } else {
            $leech['effective_image'] = null;
        }
    }
    
    echo json_encode([
        'leeches' => $leeches,
        'count' => count($leeches),
        'tips' => [
            'Prima di riattivare, considera:',
            '1. Dividi la carta in 2-3 carte più specifiche',
            '2. Aggiungi un\'immagine (dual coding)',
            '3. Crea una mnemonica',
            '4. Verifica le conoscenze prerequisite'
        ]
    ]);
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
}
