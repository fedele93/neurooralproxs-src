<?php
/**
 * API Review - Algoritmo SM-2 Migliorato
 * 
 * Miglioramenti rispetto alla versione base:
 * 1. Traccia i "lapses" (fallimenti) per identificare carte problematiche
 * 2. Applica penalità progressive per carte difficili ripetute
 * 3. Registra la data dell'ultima review per analisi future
 * 4. Restituisce informazioni più dettagliate per feedback all'utente
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

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
    
    /**
     * ALGORITMO SM-2 MIGLIORATO
     * 
     * Perché questa formula? (Riferimento: SuperMemo documentation)
     * 
     * L'Easiness Factor (EF) rappresenta quanto è "facile" una carta per te.
     * - EF alto (es. 2.5) = carta facile, intervalli crescono rapidamente
     * - EF basso (es. 1.3) = carta difficile, intervalli crescono lentamente
     * 
     * La formula: EF' = EF + (0.1 - (5-q) * (0.08 + (5-q) * 0.02))
     * dove q è la qualità della risposta (1-5)
     * 
     * Se rispondi:
     * - q=5 (perfetto): EF aumenta di +0.10
     * - q=4 (buono):    EF aumenta di +0.04  
     * - q=3 (ok):       EF rimane stabile
     * - q=2 (difficile): EF diminuisce di -0.14
     * - q=1 (fallito):  EF diminuisce di -0.30
     */
    
    // Calcola il nuovo Easiness Factor
    $newEF = $oldEF + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
    
    // EF minimo è 1.3 (evita intervalli troppo corti che non permettono consolidamento)
    $newEF = max(1.3, $newEF);
    
    // Variabili per il nuovo scheduling
    $newInterval = 0;
    $newReps = 0;
    $newLapses = $lapses;
    
    if ($quality < 3) {
        /**
         * RISPOSTA DIFFICILE/FALLITA
         * 
         * Quando fallisci una carta, il cervello non ha consolidato quella memoria.
         * Secondo la curva dell'oblio di Ebbinghaus, devi rivederla presto.
         * 
         * Incrementiamo i lapses per identificare carte cronicamente difficili.
         * Queste carte potrebbero necessitare di essere divise in unità più piccole
         * (principio dell'informazione minima - riferimento [131][133]).
         */
        $newInterval = 1; // Rivedi domani
        $newReps = 0;     // Reset delle ripetizioni consecutive
        $newLapses = $lapses + 1; // Incrementa contatore fallimenti
        
        /**
         * PENALITÀ PER LAPSES MULTIPLI
         * 
         * Se una carta ha molti lapses, riduciamo ulteriormente l'EF.
         * Questo crea intervalli più corti per carte problematiche.
         * 
         * Dopo 3+ lapses, suggeriamo che la carta potrebbe dover essere
         * riformulata secondo il principio dell'informazione minima.
         */
        if ($newLapses >= 3) {
            $newEF = max(1.3, $newEF - 0.1);
        }
        
    } else {
        /**
         * RISPOSTA CORRETTA
         * 
         * Gli intervalli seguono una progressione basata sullo spacing effect.
         * Ogni revisione riuscita rafforza la traccia di memoria (LTP).
         * 
         * Progressione standard SM-2:
         * - Prima revisione corretta: 1 giorno
         * - Seconda revisione corretta: 6 giorni  
         * - Successive: intervallo precedente × EF
         */
        $newReps = $oldReps + 1;
        
        if ($newReps == 1) {
            $newInterval = 1;
        } elseif ($newReps == 2) {
            $newInterval = 6;
        } else {
            $newInterval = round($oldInterval * $newEF);
        }
        
        /**
         * BONUS PER RISPOSTA PERFETTA
         * 
         * Se rispondi "Facile" (quality = 5), il cervello ha consolidato
         * molto bene. Possiamo allungare leggermente l'intervallo.
         * 
         * Questo implementa il concetto di "desirable difficulty" [130]:
         * non vogliamo rivedere troppo presto carte già ben consolidate,
         * perché questo ridurrebbe l'efficacia del retrieval practice.
         */
        if ($quality == 5 && $newReps > 2) {
            $newInterval = round($newInterval * 1.1);
        }
    }
    
    // Calcola la data della prossima review
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
            last_review = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $newEF, 
        $newInterval, 
        $newReps, 
        $next_review, 
        $newLapses,
        $today,
        $data['card_id']
    ]);
    
    // Prepara risposta con informazioni utili
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
    
    // Avviso se la carta ha troppi lapses (suggerimento per l'utente)
    if ($newLapses >= 3) {
        $response['warning'] = 'Questa carta ha molti fallimenti. Considera di dividerla in parti più piccole.';
    }
    
    echo json_encode($response);
    
} else {
    /**
     * GET: Recupera una carta da ripassare
     * 
     * Ordinamento per priorità:
     * 1. Carte con molti lapses (hanno bisogno di più attenzione)
     * 2. Carte con next_review più vecchio (più urgenti)
     * 
     * L'interleaving avverrà naturalmente perché prendiamo carte
     * da categorie diverse basandoci sulla data di scadenza.
     */
    $stmt = $db->query("
        SELECT * FROM flashcards 
        WHERE user_id = 1 
        AND next_review <= date('now') 
        ORDER BY 
            COALESCE(lapses, 0) DESC,
            next_review ASC
        LIMIT 1
    ");
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Conta quante carte sono da ripassare oggi
    $countStmt = $db->query("
        SELECT COUNT(*) as remaining 
        FROM flashcards 
        WHERE user_id = 1 
        AND next_review <= date('now')
    ");
    $remaining = $countStmt->fetch()['remaining'];
    
    echo json_encode([
        'card' => $card ?: null,
        'remaining_today' => $remaining
    ]);
}
