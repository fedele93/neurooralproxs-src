<?php
/**
 * API Stats - Statistiche con Streak e Motivazione
 * 
 * NOVITÀ:
 * - Traccia i giorni consecutivi di studio (streak)
 * - Conta le carte riviste oggi
 * - Salva il record personale di streak
 * - Calcola statistiche motivazionali
 * 
 * PERCHÉ TRACCIARE LE STREAK? (Base scientifica)
 * La ricerca sulla formazione di abitudini mostra che:
 * - Visualizzare i progressi aumenta la motivazione intrinseca
 * - Le streak creano "commitment devices" psicologici
 * - Celebrare piccoli successi rafforza il comportamento
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();

// === File per persistenza streak (semplice, senza modifiche al DB) ===
$streakFile = '/tmp/neurooral_streak_data.json';

/**
 * Carica i dati streak dal file
 */
function loadStreakData($file) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) return $data;
    }
    return [
        'last_study_date' => null,
        'current_streak' => 0,
        'best_streak' => 0,
        'total_days_studied' => 0,
        'daily_goal' => 20  // Obiettivo default: 20 carte/giorno
    ];
}

/**
 * Salva i dati streak
 */
function saveStreakData($file, $data) {
    file_put_contents($file, json_encode($data));
}

/**
 * Aggiorna la streak basandosi sull'attività di oggi
 */
function updateStreak($streakData, $reviewedToday) {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Se non ha studiato oggi, non aggiornare la streak
    if ($reviewedToday == 0) {
        // Controlla se la streak è "morta" (non ha studiato ieri)
        if ($streakData['last_study_date'] !== null && 
            $streakData['last_study_date'] !== $today && 
            $streakData['last_study_date'] !== $yesterday) {
            // Streak interrotta!
            $streakData['current_streak'] = 0;
        }
        return $streakData;
    }
    
    // Ha studiato oggi
    if ($streakData['last_study_date'] === $today) {
        // Già contato oggi, non fare nulla
        return $streakData;
    }
    
    if ($streakData['last_study_date'] === $yesterday) {
        // Ha studiato anche ieri: incrementa streak
        $streakData['current_streak']++;
    } elseif ($streakData['last_study_date'] === null || 
              $streakData['last_study_date'] !== $today) {
        // Prima volta o streak interrotta: ricomincia da 1
        if ($streakData['last_study_date'] !== $yesterday && 
            $streakData['last_study_date'] !== null) {
            $streakData['current_streak'] = 1;
        } else {
            $streakData['current_streak'] = max(1, $streakData['current_streak'] + 1);
        }
    }
    
    // Aggiorna record se necessario
    if ($streakData['current_streak'] > $streakData['best_streak']) {
        $streakData['best_streak'] = $streakData['current_streak'];
    }
    
    // Aggiorna data ultimo studio
    if ($streakData['last_study_date'] !== $today) {
        $streakData['total_days_studied']++;
        $streakData['last_study_date'] = $today;
    }
    
    return $streakData;
}

// === STATISTICHE BASE ===
$total = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1")->fetch()['count'];
$due = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND next_review <= date('now') AND (suspended IS NULL OR suspended = 0)")->fetch()['count'];
$new = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND repetitions = 0 AND (suspended IS NULL OR suspended = 0)")->fetch()['count'];
$leeches = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1 AND suspended = 1")->fetch()['count'];
$avgEF = $db->query("SELECT AVG(easiness_factor) as avg_ef FROM flashcards WHERE user_id = 1 AND (suspended IS NULL OR suspended = 0)")->fetch()['avg_ef'];

// === CARTE RIVISTE OGGI ===
$reviewedToday = 0;
$stmt = $db->query("
    SELECT COUNT(*) as count 
    FROM flashcards 
    WHERE user_id = 1 AND date(last_review) = date('now')
");
if ($stmt) {
    $reviewedToday = (int)$stmt->fetch()['count'];
}

// === GESTIONE STREAK ===
$streakData = loadStreakData($streakFile);
$streakData = updateStreak($streakData, $reviewedToday);
saveStreakData($streakFile, $streakData);

// === STATISTICHE SETTIMANALI ===
$weekStats = $db->query("
    SELECT 
        date(last_review) as study_date,
        COUNT(*) as cards_reviewed
    FROM flashcards 
    WHERE user_id = 1 
    AND last_review >= date('now', '-7 days')
    AND last_review IS NOT NULL
    GROUP BY date(last_review)
    ORDER BY study_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calcola media carte/giorno questa settimana
$totalCardsWeek = array_sum(array_column($weekStats, 'cards_reviewed'));
$daysStudiedWeek = count($weekStats);
$avgCardsPerDay = $daysStudiedWeek > 0 ? round($totalCardsWeek / $daysStudiedWeek) : 0;

// === BREAKDOWN PER CATEGORIA ===
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

// === CARTE PROBLEMATICHE ===
$problematicCards = $db->query("
    SELECT id, question, category, lapses, easiness_factor, suspended
    FROM flashcards 
    WHERE user_id = 1 AND lapses >= 3
    ORDER BY suspended DESC, lapses DESC
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);

// === LEECHES ===
$leechCards = $db->query("
    SELECT id, question, answer, category, lapses, easiness_factor
    FROM flashcards 
    WHERE user_id = 1 AND suspended = 1
    ORDER BY lapses DESC
")->fetchAll(PDO::FETCH_ASSOC);

// === PREVISIONE SETTIMANA ===
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

// === RISPOSTA JSON ===
echo json_encode([
    // Statistiche base
    'total' => (int)$total,
    'due' => (int)$due,
    'new' => (int)$new,
    'leeches' => (int)$leeches,
    'avg_ef' => round((float)$avgEF, 2),
    'reviewed_today' => $reviewedToday,
    
    // === NUOVO: Statistiche Streak ===
    'streak' => [
        'current' => $streakData['current_streak'],
        'best' => $streakData['best_streak'],
        'total_days' => $streakData['total_days_studied'],
        'daily_goal' => $streakData['daily_goal'],
        'goal_reached' => $reviewedToday >= $streakData['daily_goal']
    ],
    
    // === NUOVO: Statistiche settimanali ===
    'week_activity' => $weekStats,
    'avg_cards_per_day' => $avgCardsPerDay,
    
    // Breakdown
    'by_category' => $categoryStats,
    'problematic_cards' => $problematicCards,
    'leech_cards' => $leechCards,
    'week_forecast' => $weekForecast,
    
    // Insights
    'insights' => generateInsights($avgEF, $categoryStats, count($problematicCards), (int)$leeches, $streakData, $reviewedToday)
], JSON_PRETTY_PRINT);

/**
 * Genera insight automatici (AGGIORNATO con streak)
 */
function generateInsights($avgEF, $categories, $problematicCount, $leechCount, $streakData, $reviewedToday) {
    $insights = [];
    
    // === NUOVO: Insight motivazionali sulla streak ===
    if ($streakData['current_streak'] >= 7) {
        $insights[] = "🔥 Fantastico! Streak di {$streakData['current_streak']} giorni! Continua così!";
    } elseif ($streakData['current_streak'] >= 3) {
        $insights[] = "🔥 Ottimo! {$streakData['current_streak']} giorni consecutivi di studio!";
    } elseif ($streakData['current_streak'] == 0 && $reviewedToday == 0) {
        $insights[] = "📚 Studia oggi per iniziare una nuova streak!";
    }
    
    // Obiettivo giornaliero
    if ($reviewedToday > 0 && $reviewedToday >= $streakData['daily_goal']) {
        $insights[] = "🎯 Obiettivo giornaliero raggiunto! ({$reviewedToday}/{$streakData['daily_goal']} carte)";
    } elseif ($reviewedToday > 0) {
        $remaining = $streakData['daily_goal'] - $reviewedToday;
        $insights[] = "🎯 Ancora {$remaining} carte per l'obiettivo giornaliero.";
    }
    
    // Record personale
    if ($streakData['current_streak'] > 0 && $streakData['current_streak'] == $streakData['best_streak']) {
        $insights[] = "🏆 Sei al tuo record personale di streak!";
    }
    
    // Leeches
    if ($leechCount > 0) {
        $insights[] = "🧛 Hai {$leechCount} carte sospese (leeches). Riformulale per riabilitarle.";
    }
    
    // EF medio
    if ($avgEF < 2.0) {
        $insights[] = "⚠️ L'EF medio è basso ({$avgEF}). Considera di semplificare le carte più difficili.";
    } elseif ($avgEF > 2.5) {
        $insights[] = "✅ Ottimo EF medio ({$avgEF}). Stai memorizzando bene!";
    }
    
    // Carte a rischio
    if ($problematicCount > 5) {
        $insights[] = "📋 Hai {$problematicCount} carte a rischio (3+ fallimenti).";
    }
    
    // Categoria più debole
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
            $insights[] = "📚 '{$weakest}' ha l'EF più basso ({$lowestEF}).";
        }
    }
    
    if (empty($insights)) {
        $insights[] = "✅ Tutto bene! Continua così.";
    }
    
    return $insights;
}
