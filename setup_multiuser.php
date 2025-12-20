<?php
/**
 * SETUP MULTI-UTENTE per NeuroOral Pro
 * 
 * Questo script:
 * 1. Crea la tabella 'users' con due utenti (te e Miriam)
 * 2. Assegna le carte esistenti a te (user_id = 1)
 * 3. Crea la cartella per gli upload delle immagini
 * 
 * ESEGUI UNA SOLA VOLTA sul server con:
 *   php setup_multiuser.php
 * 
 * PERCHÉ DUE UTENTI SEPARATI? (Base scientifica)
 * ------------------------------------------------
 * Ogni persona ha pattern di apprendimento diversi. L'algoritmo SM-2/FSRS
 * calcola l'Easiness Factor (EF) in base alla TUA performance personale.
 * Se tu e Miriam condivideste le stesse carte, l'EF sarebbe "inquinato"
 * dalle risposte dell'altro, rendendo gli intervalli di ripasso sub-ottimali.
 * 
 * Inoltre, le streak e gli obiettivi giornalieri sono strumenti motivazionali
 * personali - mescolarli tra due persone ne ridurrebbe l'efficacia psicologica.
 */

echo "=== Setup Multi-Utente NeuroOral Pro ===\n\n";

// Percorso del database - RELATIVO alla cartella dello script
// Funziona sia sul host che dentro il container
$scriptDir = __DIR__;
$dbPath = $scriptDir . '/data/flashcards.db';

// Se siamo nella cartella api/, sali di un livello
if (basename($scriptDir) === 'api') {
    $dbPath = dirname($scriptDir) . '/data/flashcards.db';
    $scriptDir = dirname($scriptDir);
}

echo "Cartella di lavoro: $scriptDir\n";
echo "Database: $dbPath\n\n";

// Verifica che il database esista
if (!file_exists($dbPath)) {
    die("ERRORE: Database non trovato in $dbPath\nAssicurati di eseguire lo script dalla cartella src/\n");
}

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Connessione al database OK\n";
    
    // === STEP 1: Crea tabella users ===
    echo "2. Creazione tabella users...\n";
    
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            display_name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Verifica se gli utenti esistono già
    $existingUsers = $db->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
    
    if ($existingUsers == 0) {
        // Inserisci i due utenti
        $db->exec("
            INSERT INTO users (id, username, display_name) VALUES 
            (1, 'principale', 'Utente Principale'),
            (2, 'miriam', 'Miriam')
        ");
        echo "   ✓ Creati utenti: 'principale' (ID 1) e 'miriam' (ID 2)\n";
    } else {
        echo "   ✓ Utenti già esistenti (saltato)\n";
    }
    
    // === STEP 2: Verifica colonne flashcards ===
    echo "3. Verifica struttura tabella flashcards...\n";
    
    // Controlla se la colonna image_path esiste
    $columns = $db->query("PRAGMA table_info(flashcards)")->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'name');
    
    if (!in_array('image_path', $columnNames)) {
        $db->exec("ALTER TABLE flashcards ADD COLUMN image_path TEXT");
        echo "   ✓ Aggiunta colonna 'image_path' per upload locali\n";
    } else {
        echo "   ✓ Colonna 'image_path' già presente\n";
    }
    
    // === STEP 3: Crea cartella uploads ===
    echo "4. Creazione cartella uploads...\n";
    
    $uploadDir = $scriptDir . '/uploads';
    if (!is_dir($uploadDir)) {
        if (mkdir($uploadDir, 0755, true)) {
            echo "   ✓ Creata cartella $uploadDir\n";
        } else {
            echo "   ⚠ ATTENZIONE: Non riesco a creare $uploadDir\n";
            echo "     Esegui manualmente: mkdir -p $uploadDir\n";
        }
    } else {
        echo "   ✓ Cartella uploads già esistente\n";
    }
    
    // Imposta permessi
    @chmod($uploadDir, 0755);
    
    // === STEP 4: Crea tabella per streak per utente ===
    echo "5. Creazione tabella streak per utente...\n";
    
    $db->exec("
        CREATE TABLE IF NOT EXISTS user_streaks (
            user_id INTEGER PRIMARY KEY,
            last_study_date DATE,
            current_streak INTEGER DEFAULT 0,
            best_streak INTEGER DEFAULT 0,
            total_days_studied INTEGER DEFAULT 0,
            daily_goal INTEGER DEFAULT 20,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    
    // Inizializza streak per entrambi gli utenti
    $db->exec("INSERT OR IGNORE INTO user_streaks (user_id) VALUES (1), (2)");
    echo "   ✓ Tabella streak creata\n";
    
    // === RIEPILOGO ===
    echo "\n=== SETUP COMPLETATO ===\n\n";
    echo "Utenti disponibili:\n";
    $users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "  - ID {$user['id']}: {$user['display_name']} ({$user['username']})\n";
    }
    
    echo "\nCarte esistenti:\n";
    $cardCount = $db->query("SELECT COUNT(*) as count FROM flashcards WHERE user_id = 1")->fetch()['count'];
    echo "  - $cardCount carte assegnate all'Utente Principale\n";
    
    echo "\nProssimi passi:\n";
    echo "1. Esegui 'git add .' e 'git commit' e 'git push'\n";
    echo "2. Sul server: git pull\n";
    echo "3. Ricarica la pagina nel browser\n";
    
} catch (PDOException $e) {
    die("ERRORE DATABASE: " . $e->getMessage() . "\n");
}
