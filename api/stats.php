<?php
/**
 * API Stats - Statistiche con Leeches
 * 
 * NOVITÀ: Include conteggio e lista delle carte sospese (leeches)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// Statistiche globali
$total = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1")->fetch()['count'];
$due = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND next_review <= date('now') AND (suspended IS NULL OR suspended = 0)")->fetch()['count'];
$new = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND repetitions = 0 AND (suspended IS NULL OR suspended = 0)")->fetch()['count'];

// === NUOVO: Conteggio leeches (carte sospese) ===
$leeches = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND suspended = 1")->fetch()['count'];

// EF medio (solo carte attive)
$avgEF = $db->query("SELECT AVG(easiness_factor) as avg_ef FROM flashcards WHERE user_id = 1 AND (suspended IS NULL OR suspended = 0)")->fetch()['avg_ef'];

// Breakdown per categoria (escluse sospese per le statistiche "due")
$categoryStats = $db->query("
    SELECT 
        COALESCE(category, 'Senza categoria') as category,
        COUNT(*) as total,
        SUM(CASE WHEN next_review <= date('now') AND (suspended IS NULL OR suspended = 0) THEN 1 ELSE 0 END) as due,
        SUM(CASE WHEN repetitions = 0 AND (suspended IS NULL OR suspended = 0) THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN suspended = 1 THEN 1 ELSE 0 END) as suspended,
        ROUND(AVG(CASE WHEN suspended IS NULL OR suspended = 0 THEN easiness_factor END), 2) as avg_ef,
        SUM(COALESCE(lapses, 0)) as total_lapses
    FROM flashcards 
    WHERE user_id = 1
    GROUP BY category
    ORDER BY due DESC, total_lapses DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Carte problematiche (3+ lapses, non ancora sospese)
$problematicCards = $db->query("
    SELECT id, question, category, lapses, easiness_factor, suspended
    FROM flashcards 
    WHERE user_id = 1 AND lapses >= 3
    ORDER BY suspended DESC, lapses DESC
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);

// === NUOVO: Lista completa leeches per gestione ===
$leechCards = $db->query("
    SELECT id, question, answer, category, lapses, easiness_factor
    FROM flashcards 
    WHERE user_id = 1 AND suspended = 1
    ORDER BY lapses DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Riviste oggi
$reviewedToday = 0;
$lastReviewCheck = $db->query("
    SELECT COUNT(*) as count 
    FROM flashcards 
    WHERE user_id = 1 AND last_review >= date('now')
");
if ($lastReviewCheck) {
    $reviewedToday = $lastReviewCheck->fetch()['count'];
}

// Previsione settimana
$weekForecast = $db->query("
    SELECT 
        date(next_review) as review_date,
        COUNT(*) as cards_due
    FROM flashcards 
    WHERE user_id = 1 
    AND next_review > date('now')
    AND next_review <= date('now', '+7 days')
    AND (suspended IS NULL OR suspended = 0)
    GROUP BY date(next_review)
    ORDER BY review_date
")->fetchAll(PDO::FETCH_ASSOC);

// Risposta JSON
echo json_encode([
    'total' => (int)$total,
    'due' => (int)$due,
    'new' => (int)$new,
    'leeches' => (int)$leeches,  // NUOVO
    'avg_ef' => round((float)$avgEF, 2),
    'reviewed_today' => (int)$reviewedToday,
    'by_category' => $categoryStats,
    'problematic_cards' => $problematicCards,
    'leech_cards' => $leechCards,  // NUOVO: lista completa per gestione
    'week_forecast' => $weekForecast,
    'insights' => generateInsights($avgEF, $categoryStats, count($problematicCards), (int)$leeches)
], JSON_PRETTY_PRINT);

/**
 * Genera insight automatici (MODIFICATO: include leeches)
 */
function generateInsights($avgEF, $categories, $problematicCount, $leechCount) {
    $insights = [];
    
    // === NUOVO: Insight su leeches ===
    if ($leechCount > 0) {
        $insights[] = "🧛 Hai {$leechCount} carte sospese (leeches). Riformulale per riabilitarle.";
    }
    
    // Insight su EF medio
    if ($avgEF < 2.0) {
        $insights[] = "⚠️ L'EF medio è basso ({$avgEF}). Considera di semplificare le carte più difficili.";
    } elseif ($avgEF > 2.5) {
        $insights[] = "✅ Ottimo EF medio ({$avgEF}). Stai memorizzando bene il materiale!";
    }
    
    // Insight su carte a rischio
    if ($problematicCount > 5) {
        $insights[] = "📋 Hai {$problematicCount} carte a rischio (3+ fallimenti). Potrebbero diventare leeches.";
    }
    
    // Insight sulla categoria più debole
    if (!empty($categories)) {
        $weakest = null;
        $lowestEF = 3.0;
        foreach ($categories as $cat) {
            if ($cat['avg_ef'] && $cat['avg_ef'] < $lowestEF && $cat['total'] >= 5) {
                $lowestEF = $cat['avg_ef'];
                $weakest = $cat['category'];
            }
        }
        if ($weakest && $lowestEF < 2.2) {
            $insights[] = "📚 La categoria '{$weakest}' ha l'EF più basso ({$lowestEF}). Potrebbe richiedere più attenzione.";
        }
    }
    
    if (empty($insights)) {
        $insights[] = "✅ Tutto bene! Continua così.";
    }
    
    return $insights;
}
