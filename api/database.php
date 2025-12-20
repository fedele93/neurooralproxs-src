<?php
/**
 * Database Connection
 * 
 * Fornisce la connessione al database SQLite e
 * gestisce la sessione per il multi-utente.
 */

// Avvia la sessione se non già avviata
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDatabase() {
    try {
        // Percorso relativo: dalla cartella api/ sali di un livello e vai in data/
        $dbPath = __DIR__ . '/../data/flashcards.db';
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
}

/**
 * Ottieni l'ID dell'utente corrente dalla sessione
 * Default: 1 (per retrocompatibilità)
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? 1;
}
