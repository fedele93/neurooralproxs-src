<?php
/**
 * API Leeches - Gestione Carte Sospese
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
             * 
             * PERCHÉ NON RESETTARE COMPLETAMENTE I LAPSES?
             * La carta ha una storia di difficoltà. Anche se riformulata,
             * manteniamo traccia parziale per monitorare se migliora davvero.
             */
            $stmt = $db->prepare('
                UPDATE flashcards 
                SET suspended = 0,
                    lapses = MAX(0, lapses / 2),
                    easiness_factor = MIN(2.5, easiness_factor + 0.2),
                    repetitions = 0,
                    interval = 1,
                    next_review = date("now", "+1 day")
                WHERE id = ? AND user_id = 1
            ');
            $stmt->execute([$cardId]);
            
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
            $stmt = $db->prepare('DELETE FROM flashcards WHERE id = ? AND user_id = 1');
            $stmt->execute([$cardId]);
            
            if ($stmt->rowCount() > 0) {
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
    $stmt = $db->query("
        SELECT id, question, answer, category, lapses, easiness_factor, 
               last_review, next_review
        FROM flashcards 
        WHERE user_id = 1 AND suspended = 1
        ORDER BY lapses DESC, last_review DESC
    ");
    $leeches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
