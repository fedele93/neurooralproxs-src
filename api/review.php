<?php
/**
 * API Review - Algoritmo SM-2 con Leeches + Interleaving
 * 
 * NOVITÀ INTERLEAVING:
 * - Alterna automaticamente tra categorie diverse
 * - Evita di mostrare 2+ carte della stessa categoria di fila
 * - Migliora discriminazione e transfer (rif. 33, 38, 49, 52, 55)
 * 
 * PERCHÉ INTERLEAVING? (Base scientifica)
 * La ricerca mostra che alternare categorie durante lo studio:
 * - Migliora la capacità di distinguere concetti simili
 * - Aumenta il transfer di apprendimento del 10-15%
 * - Simula meglio le condizioni di esame reale
 * - Forza il cervello a "ricaricare" il contesto, rafforzando la memoria
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// === COSTANTI CONFIGURABILI ===
define('LEECH_THRESHOLD', 4);

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
    
    // Calcola il nuovo Easiness Factor
    $newEF = $oldEF + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
    $newEF = max(1.3, $newEF);
    
    $newInterval = 0;
    $newReps = 0;
    $newLapses = $lapses;
    $becameLeech = false;
    $suspended = 0;
    
    if ($quality < 3) {
        $newInterval = 1;
        $newReps = 0;
        $newLapses = $lapses + 1;
        
        if ($newLapses >= 3) {
            $newEF = max(1.3, $newEF - 0.1);
        }
        
        if ($newLapses >= LEECH_THRESHOLD) {
            $suspended = 1;
            $becameLeech = true;
        }
        
    } else {
        $newReps = $oldReps + 1;
        
        if ($newReps == 1) {
            $newInterval = 1;
        } elseif ($newReps == 2) {
            $newInterval = 6;
        } else {
            $newInterval = round($oldInterval * $newEF);
        }
        
        if ($quality == 5 && $newReps > 2) {
            $newInterval = round($newInterval * 1.1);
        }
    }
    
    $next_review = date('Y-m-d', strtotime("+{$newInterval} days"));
    $today = date('Y-m-d H:i:s');
    
    // Aggiorna il database
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
    
    /**
     * === NUOVO: Salva l'ultima categoria ripassata per interleaving ===
     * Usiamo una tabella separata o la sessione. Per semplicità, usiamo un file.
     */
    $lastCategoryFile = '/tmp/neurooral_last_category_' . ($card['user_id'] ?? 1) . '.txt';
    file_put_contents($lastCategoryFile, $card['category'] ?? '');
    
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
    
    if ($becameLeech) {
        $response['leech'] = true;
        $response['warning'] = '🧛 Carta sospesa! Ha raggiunto ' . LEECH_THRESHOLD . ' fallimenti. Riformulala in carte più piccole.';
    } elseif ($newLapses >= 3) {
        $response['warning'] = '⚠️ Attenzione: ' . $newLapses . '/' . LEECH_THRESHOLD . ' fallimenti. Considera di semplificare questa carta.';
    }
    
    echo json_encode($response);
    
} else {
    /**
     * === NUOVO: GET con INTERLEAVING ===
     * 
     * Strategia:
     * 1. Leggi l'ultima categoria ripassata
     * 2. Cerca prima carte di ALTRE categorie
     * 3. Se non ce ne sono, prendi dalla stessa categoria
     * 
     * Questo garantisce massimo mescolamento tra argomenti diversi.
     */
    
    // Leggi l'ultima categoria ripassata
    $userId = 1; // Per ora fisso, in futuro da sessione
    $lastCategoryFile = '/tmp/neurooral_last_category_' . $userId . '.txt';
    $lastCategory = '';
    if (file_exists($lastCategoryFile)) {
        $lastCategory = trim(file_get_contents($lastCategoryFile));
    }
    
    $card = null;
    
    /**
     * PASSO 1: Cerca una carta di categoria DIVERSA dall'ultima
     * 
     * Perché? L'interleaving forza il cervello a "cambiare contesto",
     * il che rafforza i percorsi di memoria indipendenti per ogni categoria.
     * Questo è particolarmente utile per distinguere:
     * - Sindromi simili (es. Wallenberg vs Weber)
     * - Farmaci della stessa classe
     * - Strutture anatomiche adiacenti
     */
    if ($lastCategory !== '') {
        $stmt = $db->prepare("
            SELECT * FROM flashcards 
            WHERE user_id = ? 
            AND next_review <= date('now')
            AND (suspended IS NULL OR suspended = 0)
            AND (category IS NULL OR category != ?)
            ORDER BY 
                COALESCE(lapses, 0) DESC,
                next_review ASC
            LIMIT 1
        ");
        $stmt->execute([$userId, $lastCategory]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * PASSO 2: Se non ci sono carte di altre categorie, prendi qualsiasi carta
     * 
     * Questo succede quando:
     * - Rimangono solo carte di una categoria
     * - È la prima carta della sessione
     * - Tutte le altre categorie sono completate
     */
    if (!$card) {
        $stmt = $db->prepare("
            SELECT * FROM flashcards 
            WHERE user_id = ? 
            AND next_review <= date('now')
            AND (suspended IS NULL OR suspended = 0)
            ORDER BY 
                COALESCE(lapses, 0) DESC,
                next_review ASC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Conta carte rimanenti
    $countStmt = $db->prepare("
        SELECT COUNT(*) as remaining 
        FROM flashcards 
        WHERE user_id = ? 
        AND next_review <= date('now')
        AND (suspended IS NULL OR suspended = 0)
    ");
    $countStmt->execute([$userId]);
    $remaining = $countStmt->fetch()['remaining'];
    
    /**
     * === NUOVO: Informazioni extra per debug/statistiche ===
     * Mostra quante categorie diverse sono ancora da ripassare
     */
    $categoriesStmt = $db->prepare("
        SELECT COUNT(DISTINCT category) as num_categories
        FROM flashcards 
        WHERE user_id = ? 
        AND next_review <= date('now')
        AND (suspended IS NULL OR suspended = 0)
    ");
    $categoriesStmt->execute([$userId]);
    $numCategories = $categoriesStmt->fetch()['num_categories'];
    
    echo json_encode([
        'card' => $card ?: null,
        'remaining_today' => $remaining,
        'categories_remaining' => $numCategories,  // Nuovo: per statistiche
        'interleaving_active' => ($lastCategory !== '' && $numCategories > 1)  // Nuovo: indica se interleaving è attivo
    ]);
}
