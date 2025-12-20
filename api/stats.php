<?php
/**
 * API Stats - Statistiche con Streak e Motivazione (AGGIORNATO)
 * 
 * MODIFICHE:
 * - Supporto multi-utente tramite sessione
 * - Streak salvata nel database (tabella user_streaks) invece che file
 * - Ogni utente ha le proprie statistiche separate
 * 
 * PERCHÉ STREAK SEPARATE? (Base scientifica)
 * ------------------------------------------
 * Le streak sono strumenti motivazionali personali.
 * Mescolarle tra utenti diversi ridurrebbe l'effetto psicologico
 * di "commitment" che le rende efficaci.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

$db = getDatabase();
$userId = getCurrentUserId();

/**
 * Aggiorna la streak basandosi sull'attività di oggi
 */
function updateStreak($db, $userId, $reviewedToday) {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Recupera dati streak correnti
    $stmt = $db->prepare("SELECT * FROM user_streaks WHERE user_id = ?");
    $stmt->execute([$userId]);
    $streakData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Se non esiste, creala
    if (!$streakData) {
        $db->prepare("INSERT INTO user_streaks (user_id) VALUES (?)")->execute([$userId]);
        $streakData = [
            'last_study_date' => null,
            'current_streak' => 0,
            'best_streak' => 0,
            'total_days_studied' => 0,
            'daily_goal' => 20
        ];
    }
    
    $lastDate = $streakData['last_study_date'];
    $currentStreak = (int)$streakData['current_streak'];
    $bestStreak = (int)$streakData['best_streak'];
    $totalDays = (int)$streakData['total_days_studied'];
    $dailyGoal = (int)$streakData['daily_goal'];
    
    // Se non ha studiato oggi, controlla se la streak è "morta"
    if ($reviewedToday == 0) {
        if ($lastDate !== null && $lastDate !== $today && $lastDate !== $yesterday) {
            $currentStreak = 0;
        }
    } else {
        // Ha studiato oggi
        if ($lastDate === $today) {
            // Già contato oggi, non fare nulla
        } elseif ($lastDate === $yesterday) {
            // Ha studiato anche ieri: incrementa streak
            $currentStreak++;
            $totalDays++;
        } else {
            // Prima volta o streak interrotta: ricomincia da 1
            $currentStreak = 1;
            $totalDays++;
        }
        
        // Aggiorna record se necessario
        if ($currentStreak > $bestStreak) {
            $bestStreak = $currentStreak;
        }
        
        // Aggiorna data ultimo studio solo se cambiata
        if ($lastDate !== $today) {
            $lastDate = $today;
        }
    }
    
    // Salva nel database
    $stmt = $db->prepare("
        UPDATE user_streaks 
        SET last_study_date = ?,
            current_streak = ?,
            best_streak = ?,
            total_days_studied = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$lastDate, $currentStreak, $bestStreak, $totalDays, $userId]);
    
    return [
        'last_study_date' => $lastDate,
        'current_streak' => $currentStreak,
        'best_streak' => $bestStreak,
        'total_days_studied' => $totalDays,
        'daily_goal' => $dailyGoal
    ];
}

// === STATISTICHE BASE ===
$stmt = $db->prepare("SELECT COUNT(*) as count FROM flashcards WHERE user_id = ?");
$stmt->execute([$userId]);
$total = $stmt->fetch()['count'];

$stmt = $db->prepare("SELECT COUNT(*) as count FROM flashcards WHERE user_id = ? AND next_review <= date('now') AND (suspended IS NULL OR suspended = 0)");
$stmt->execute([$userId]);
$due = $stmt->fetch()['count'];

$stmt = $db->prepare("SELECT COUNT(*) as count FROM flashcards WHERE user_id = ? AND repetitions = 0 AND (suspended IS NULL OR suspended = 0)");
$stmt->execute([$userId]);
$new = $stmt->fetch()['count'];

$stmt = $db->prepare("SELECT COUNT(*) as count FROM flashcards WHERE user_id = ? AND suspended = 1");
$stmt->execute([$userId]);
$leeches = $stmt->fetch()['count'];

$stmt = $db->prepare("SELECT AVG(easiness_factor) as avg_ef FROM flashcards WHERE user_id = ? AND (suspended IS NULL OR suspended = 0)");
$stmt->execute([$userId]);
$avgEF = $stmt->fetch()['avg_ef'];

// === CARTE RIVISTE OGGI ===
$stmt = $db->prepare("SELECT COUNT(*) as count FROM flashcards WHERE user_id = ? AND date(last_review) = date('now')");
$stmt->execute([$userId]);
$reviewedToday = (int)$stmt->fetch()['count'];

// === GESTIONE STREAK ===
$streakData = updateStreak($db, $userId, $reviewedToday);

// === STATISTICHE SETTIMANALI ===
$stmt = $db->prepare("
    SELECT 
        date(last_review) as study_date,
        COUNT(*) as cards_reviewed
    FROM flashcards 
    WHERE user_id = ? 
    AND last_review >= date('now', '-7 days')
    AND last_review IS NOT NULL
    GROUP BY date(last_review)
    ORDER BY study_date DESC
");
$stmt->execute([$userId]);
$weekStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCardsWeek = array_sum(array_column($weekStats, 'cards_reviewed'));
$daysStudiedWeek = count($weekStats);
$avgCardsPerDay = $daysStudiedWeek > 0 ? round($totalCardsWeek / $daysStudiedWeek) : 0;

// === BREAKDOWN PER CATEGORIA ===
$stmt = $db->prepare("
    SELECT 
        COALESCE(category, 'Senza categoria') as category,
        COUNT(*) as total,
        SUM(CASE WHEN next_review <= date('now') AND (suspended IS NULL OR suspended = 0) THEN 1 ELSE 0 END) as due,
        SUM(CASE WHEN repetitions = 0 AND (suspended IS NULL OR suspended = 0) THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN suspended = 1 THEN 1 ELSE 0 END) as suspended,
        ROUND(AVG(CASE WHEN suspended IS NULL OR suspended = 0 THEN easiness_factor END), 2) as avg_ef,
        SUM(COALESCE(lapses, 0)) as total_lapses
    FROM flashcards 
    WHERE user_id = ?
    GROUP BY category
    ORDER BY due DESC, total_lapses DESC
");
$stmt->execute([$userId]);
$categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === CARTE PROBLEMATICHE ===
$stmt = $db->prepare("
    SELECT id, question, category, lapses, easiness_factor, suspended
    FROM flashcards 
    WHERE user_id = ? AND lapses >= 3
    ORDER BY suspended DESC, lapses DESC
    LIMIT 15
");
$stmt->execute([$userId]);
$problematicCards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === LEECHES ===
$stmt = $db->prepare("
    SELECT id, question, answer, category, lapses, easiness_factor
    FROM flashcards 
    WHERE user_id = ? AND suspended = 1
    ORDER BY lapses DESC
");
$stmt->execute([$userId]);
$leechCards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === PREVISIONE SETTIMANA ===
$stmt = $db->prepare("
    SELECT 
        date(next_review) as review_date,
        COUNT(*) as cards_due
    FROM flashcards 
    WHERE user_id = ? 
    AND next_review > date('now')
    AND next_review <= date('now', '+7 days')
    AND (suspended IS NULL OR suspended = 0)
    GROUP BY date(next_review)
    ORDER BY review_date
");
$stmt->execute([$userId]);
$weekForecast = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === RISPOSTA JSON ===
echo json_encode([
    // Statistiche base
    'total' => (int)$total,
    'due' => (int)$due,
    'new' => (int)$new,
    'leeches' => (int)$leeches,
    'avg_ef' => round((float)$avgEF, 2),
    'reviewed_today' => $reviewedToday,
    'user_id' => $userId,
    
    // Statistiche Streak
    'streak' => [
        'current' => $streakData['current_streak'],
        'best' => $streakData['best_streak'],
        'total_days' => $streakData['total_days_studied'],
        'daily_goal' => $streakData['daily_goal'],
        'goal_reached' => $reviewedToday >= $streakData['daily_goal']
    ],
    
    // Statistiche settimanali
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
 * Genera insight automatici
 */
function generateInsights($avgEF, $categories, $problematicCount, $leechCount, $streakData, $reviewedToday) {
    $insights = [];
    
    // Insight motivazionali sulla streak
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
        $insights[] = "⚠️ L'EF medio è basso (" . round($avgEF, 2) . "). Considera di semplificare le carte più difficili.";
    } elseif ($avgEF > 2.5) {
        $insights[] = "✅ Ottimo EF medio (" . round($avgEF, 2) . "). Stai memorizzando bene!";
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
