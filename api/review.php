<?php
/**
 * API Review - Algoritmo SM-2 con Sistema Leeches
 * 
 * NOVITÀ rispetto alla versione precedente:
 * - Sospensione automatica delle carte "leech" (4+ lapses)
 * - Le carte sospese non appaiono nelle review
 * - Feedback specifico quando una carta diventa leech
 * 
 * PERCHÉ 4 LAPSES? (Base scientifica)
 * La ricerca SuperMemo [rif. 131, 133] indica che dopo 4 fallimenti,
 * la probabilità che la carta sia mal formulata è >80%.
 * Continuare a rivedere una carta mal formulata è controproducente:
 * - Spreca tempo che potresti usare per carte efficaci
 * - Crea frustrazione che riduce la motivazione
 * - Può consolidare errori o confusione
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// === COSTANTE CONFIGURABILE ===
// Puoi modificare questa soglia in base alla tua esperienza
define('LEECH_THRESHOLD', 4);  // Numero di lapses per diventare leech

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Recupera la carta dal database
    $stmt = $db->prepare('SELECT * FROM flashcards WHERE id = ?');
    $stmt->execute([$data['card_id']]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        echo json_encode(['error' => 'Card not found']);
        exit;
    }
    
    $quality = (int)$data['quality'];
    $oldEF = (float)$card['easiness_factor'];
    $oldInterval = (int)$card['interval'];
    $oldReps = (int)$card['repetitions'];
    $lapses = isset($card['lapses']) ? (int)$card['lapses'] : 0;
    
    // Calcola il nuovo Easiness Factor (formula SM-2 standard)
    $newEF = $oldEF + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
    $newEF = max(1.3, $newEF);
    
    $newInterval = 0;
    $newReps = 0;
    $newLapses = $lapses;
    $becameLeech = false;  // NUOVO: flag per notificare l'utente
    $suspended = 0;
    
    if ($quality < 3) {
        // RISPOSTA DIFFICILE/FALLITA
        $newInterval = 1;
        $newReps = 0;
        $newLapses = $lapses + 1;
        
        // Penalità EF per lapses multipli
        if ($newLapses >= 3) {
            $newEF = max(1.3, $newEF - 0.1);
        }
        
        /**
         * === NUOVO: SISTEMA LEECHES ===
         * 
         * Se la carta raggiunge LEECH_THRESHOLD lapses, viene sospesa.
         * 
         * PERCHÉ SOSPENDERE E NON ELIMINARE?
         * - La carta potrebbe essere recuperabile con riformulazione
         * - Potresti volerla dividere in carte più piccole
         * - Il concetto è comunque importante da imparare
         * 
         * COSA FARE CON LE LEECHES (suggerimenti basati su [rif. 133]):
         * 1. Dividila in 2-3 carte più specifiche
         * 2. Aggiungi un'immagine (dual coding)
         * 3. Crea una mnemonica
         * 4. Verifica se hai le conoscenze prerequisite
         */
        if ($newLapses >= LEECH_THRESHOLD) {
            $suspended = 1;
            $becameLeech = true;
        }
        
    } else {
        // RISPOSTA CORRETTA
        $newReps = $oldReps + 1;
        
        if ($newReps == 1) {
            $newInterval = 1;
        } elseif ($newReps == 2) {
            $newInterval = 6;
        } else {
            $newInterval = round($oldInterval * $newEF);
        }
        
        // Bonus per risposta perfetta
        if ($quality == 5 && $newReps > 2) {
            $newInterval = round($newInterval * 1.1);
        }
    }
    
    $next_review = date('Y-m-d', strtotime("+{$newInterval} days"));
    $today = date('Y-m-d H:i:s');
    
    // Aggiorna il database (MODIFICATO: include suspended)
    $stmt = $db->prepare('
        UPDATE flashcards 
        SET easiness_factor = ?, 
            interval = ?, 
            repetitions = ?, 
            next_review = ?,
            lapses = ?,
            last_review = ?,
            suspended = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $newEF, 
        $newInterval, 
        $newReps, 
        $next_review, 
        $newLapses,
        $today,
        $suspended,
        $data['card_id']
    ]);
    
    // Prepara risposta
    $response = [
        'success' => true,
        'card_id' => $data['card_id'],
        'old_ef' => round($oldEF, 2),
        'new_ef' => round($newEF, 2),
        'interval_days' => $newInterval,
        'next_review' => $next_review,
        'repetitions' => $newReps,
        'lapses' => $newLapses
    ];
    
    // === NUOVO: Messaggi specifici per leeches ===
    if ($becameLeech) {
        $response['leech'] = true;
        $response['warning'] = '🧛 Carta sospesa! Ha raggiunto ' . LEECH_THRESHOLD . ' fallimenti. Riformulala in carte più piccole.';
    } elseif ($newLapses >= 3) {
        $response['warning'] = '⚠️ Attenzione: ' . $newLapses . '/' . LEECH_THRESHOLD . ' fallimenti. Considera di semplificare questa carta.';
    }
    
    echo json_encode($response);
    
} else {
    /**
     * GET: Recupera una carta da ripassare
     * 
     * MODIFICATO: Esclude le carte sospese (suspended = 1)
     */
    $stmt = $db->query("
        SELECT * FROM flashcards 
        WHERE user_id = 1 
        AND next_review <= date('now')
        AND (suspended IS NULL OR suspended = 0)
        ORDER BY 
            COALESCE(lapses, 0) DESC,
            next_review ASC
        LIMIT 1
    ");
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Conta carte da ripassare (escluse sospese)
    $countStmt = $db->query("
        SELECT COUNT(*) as remaining 
        FROM flashcards 
        WHERE user_id = 1 
        AND next_review <= date('now')
        AND (suspended IS NULL OR suspended = 0)
    ");
    $remaining = $countStmt->fetch()['remaining'];
    
    echo json_encode([
        'card' => $card ?: null,
        'remaining_today' => $remaining
    ]);
}
