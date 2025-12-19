<?php
/**
 * API Stats - Statistiche Migliorate
 * 
 * Questo endpoint fornisce:
 * 1. Statistiche globali (totale, da ripassare, nuove)
 * 2. Breakdown per categoria (per identificare aree deboli)
 * 3. Carte problematiche (molti lapses)
 * 4. Statistiche di performance (EF medio, trend)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// Statistiche globali
$total = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1")->fetch()['count'];
$due = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND next_review <= date('now')")->fetch()['count'];
$new = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND repetitions = 0")->fetch()['count'];

/**
 * EASINESS FACTOR MEDIO
 * 
 * Un EF medio alto (>2.3) indica che stai memorizzando bene il materiale.
 * Un EF medio basso (<2.0) indica difficoltà generale - potresti dover:
 * - Semplificare le carte (principio informazione minima)
 * - Aggiungere immagini (dual coding)
 * - Studiare più frequentemente
 */
$avgEF = $db->query("SELECT AVG(easiness_factor) as avg_ef FROM flashcards WHERE user_id = 1")->fetch()['avg_ef'];

/**
 * BREAKDOWN PER CATEGORIA
 * 
 * Questo è fondamentale per capire dove concentrare lo sforzo.
 * Categorie con:
 * - Molte carte "due" = urgenti da ripassare
 * - Molti lapses = argomento difficile, valuta di rivedere le carte
 * - EF basso medio = difficoltà consolidata, serve più pratica
 */
$categoryStats = $db->query("
    SELECT 
        COALESCE(category, 'Senza categoria') as category,
        COUNT(*) as total,
        SUM(CASE WHEN next_review <= date('now') THEN 1 ELSE 0 END) as due,
        SUM(CASE WHEN repetitions = 0 THEN 1 ELSE 0 END) as new,
        ROUND(AVG(easiness_factor), 2) as avg_ef,
        SUM(COALESCE(lapses, 0)) as total_lapses
    FROM flashcards 
    WHERE user_id = 1
    GROUP BY category
    ORDER BY due DESC, total_lapses DESC
")->fetchAll(PDO::FETCH_ASSOC);

/**
 * CARTE PROBLEMATICHE
 * 
 * Carte con 3+ lapses meritano attenzione speciale.
 * Secondo il principio dell'informazione minima (SuperMemo [133]),
 * una carta troppo difficile spesso contiene troppe informazioni.
 * 
 * Consiglio: dividi queste carte in unità più piccole.
 * Es: "Descrivi la via piramidale" diventa 3-4 carte separate
 * su origine, decorso, decussazione, e innervazione.
 */
$problematicCards = $db->query("
    SELECT id, question, category, lapses, easiness_factor
    FROM flashcards 
    WHERE user_id = 1 AND lapses >= 3
    ORDER BY lapses DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

/**
 * CARTE RIVISTE OGGI
 * 
 * Tracciare l'attività giornaliera aiuta a mantenere la motivazione.
 * Gli studi sulla formazione di abitudini mostrano che vedere i
 * propri progressi aumenta la probabilità di continuare.
 */
$reviewedToday = 0;
$lastReviewCheck = $db->query("
    SELECT COUNT(*) as count 
    FROM flashcards 
    WHERE user_id = 1 AND last_review >= date('now')
");
if ($lastReviewCheck) {
    $reviewedToday = $lastReviewCheck->fetch()['count'];
}

/**
 * PREVISIONE CARICO SETTIMANA
 * 
 * Sapere quante carte arriveranno nei prossimi giorni
 * aiuta a pianificare le sessioni di studio.
 */
$weekForecast = $db->query("
    SELECT 
        date(next_review) as review_date,
        COUNT(*) as cards_due
    FROM flashcards 
    WHERE user_id = 1 
    AND next_review > date('now')
    AND next_review <= date('now', '+7 days')
    GROUP BY date(next_review)
    ORDER BY review_date
")->fetchAll(PDO::FETCH_ASSOC);

// Risposta JSON completa
echo json_encode([
    // Statistiche base
    'total' => (int)$total,
    'due' => (int)$due,
    'new' => (int)$new,
    'avg_ef' => round((float)$avgEF, 2),
    'reviewed_today' => (int)$reviewedToday,
    
    // Breakdown per categoria (per identificare aree deboli)
    'by_category' => $categoryStats,
    
    // Carte che richiedono attenzione
    'problematic_cards' => $problematicCards,
    
    // Previsione carico settimanale
    'week_forecast' => $weekForecast,
    
    // Interpretazione automatica
    'insights' => generateInsights($avgEF, $categoryStats, count($problematicCards))
], JSON_PRETTY_PRINT);

/**
 * Genera insight automatici basati sui dati
 */
function generateInsights($avgEF, $categories, $problematicCount) {
    $insights = [];
    
    // Insight su EF medio
    if ($avgEF < 2.0) {
        $insights[] = "⚠️ L'EF medio è basso ({$avgEF}). Considera di semplificare le carte più difficili.";
    } elseif ($avgEF > 2.5) {
        $insights[] = "✅ Ottimo EF medio ({$avgEF}). Stai memorizzando bene il materiale!";
    }
    
    // Insight su carte problematiche
    if ($problematicCount > 5) {
        $insights[] = "📋 Hai {$problematicCount} carte con molti fallimenti. Riformulale secondo il principio dell'informazione minima.";
    }
    
    // Insight sulla categoria più debole
    if (!empty($categories)) {
        $weakest = null;
        $lowestEF = 3.0;
        foreach ($categories as $cat) {
            if ($cat['avg_ef'] < $lowestEF && $cat['total'] >= 5) {
                $lowestEF = $cat['avg_ef'];
                $weakest = $cat['category'];
            }
        }
        if ($weakest && $lowestEF < 2.2) {
            $insights[] = "📚 La categoria '{$weakest}' ha l'EF più basso ({$lowestEF}). Potrebbe richiedere più attenzione.";
        }
    }
    
    return $insights;
}
