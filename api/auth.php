<?php
/**
 * API Auth - Gestione Utenti
 * 
 * Questo endpoint gestisce:
 * - Lista utenti disponibili
 * - Switch tra utenti
 * - Stato sessione corrente
 * 
 * NOTA: Sistema semplificato senza password
 * -----------------------------------------
 * Questo è un sistema per uso personale/familiare su rete locale.
 * Non c'è autenticazione con password perché:
 * 1. È usato solo da te e Miriam
 * 2. Gira sul tuo miniPC locale
 * 3. Aggiungere password complicherebbe l'uso quotidiano
 * 
 * Se in futuro vorrai protezione con password, possiamo aggiungerla.
 */

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/database.php';

$db = getDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    /**
     * GET: Ottieni stato sessione e lista utenti
     * 
     * Risposta:
     * - current_user: utente corrente (id, username, display_name)
     * - users: lista di tutti gli utenti disponibili
     */
    
    // Utente corrente (default: 1)
    $currentUserId = $_SESSION['user_id'] ?? 1;
    
    // Recupera info utente corrente
    $stmt = $db->prepare("SELECT id, username, display_name FROM users WHERE id = ?");
    $stmt->execute([$currentUserId]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Se l'utente non esiste, usa il default
    if (!$currentUser) {
        $currentUserId = 1;
        $_SESSION['user_id'] = 1;
        $stmt->execute([1]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lista tutti gli utenti
    $users = $db->query("SELECT id, username, display_name FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'current_user' => $currentUser,
        'users' => $users
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * POST: Switch utente
     * 
     * Parametri (JSON body):
     * - user_id: ID dell'utente a cui passare
     * 
     * Risposta:
     * - success: true/false
     * - user: info del nuovo utente
     */
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'user_id mancante']);
        exit;
    }
    
    $userId = (int)$data['user_id'];
    
    // Verifica che l'utente esista
    $stmt = $db->prepare("SELECT id, username, display_name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Utente non trovato']);
        exit;
    }
    
    // Imposta la sessione
    $_SESSION['user_id'] = $userId;
    
    echo json_encode([
        'success' => true,
        'message' => 'Passato a ' . $user['display_name'],
        'user' => $user
    ]);
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
}
