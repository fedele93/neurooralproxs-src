<?php
/**
 * API Upload - Gestione Upload Immagini
 * 
 * Questo endpoint permette di caricare immagini per le flashcard.
 * Supporta: JPG, PNG, GIF, WebP
 * Dimensione massima: 5MB
 * 
 * PERCHÉ UPLOAD LOCALE? (Base scientifica - Dual Coding [76][79][82])
 * ------------------------------------------------------------------
 * La Dual Coding Theory dimostra che combinare testo e immagini
 * crea DUE tracce di memoria indipendenti, migliorando significativamente
 * la ritenzione. Per la neurologia, questo è cruciale:
 * 
 * - Anatomia: immagini di sezioni cerebrali, circuiti neurali
 * - Neuroimaging: TC, RM, angiografie
 * - Patologia: aspetto macroscopico/microscopico delle lesioni
 * 
 * Con l'upload locale puoi fotografare direttamente dai tuoi libri
 * o appunti, senza dover prima caricare su servizi esterni.
 */

header('Content-Type: application/json');
session_start();

// === CONFIGURAZIONE ===
// Percorso relativo: dalla cartella api/ sali di un livello
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Ottieni user_id dalla sessione (default 1 per retrocompatibilità)
$userId = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * POST: Upload di una nuova immagine
     * 
     * Parametri:
     * - image: file immagine (form-data)
     * 
     * Risposta:
     * - success: true/false
     * - image_path: percorso relativo dell'immagine salvata
     * - image_url: URL completo per visualizzare l'immagine
     */
    
    // Verifica che sia stato inviato un file
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        http_response_code(400);
        echo json_encode(['error' => 'Nessun file caricato']);
        exit;
    }
    
    $file = $_FILES['image'];
    
    // Verifica errori di upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File troppo grande (limite server)',
            UPLOAD_ERR_FORM_SIZE => 'File troppo grande (limite form)',
            UPLOAD_ERR_PARTIAL => 'Upload incompleto',
            UPLOAD_ERR_NO_TMP_DIR => 'Cartella temporanea mancante',
            UPLOAD_ERR_CANT_WRITE => 'Errore scrittura disco',
        ];
        $errorMsg = $errorMessages[$file['error']] ?? 'Errore sconosciuto';
        http_response_code(400);
        echo json_encode(['error' => $errorMsg]);
        exit;
    }
    
    // Verifica dimensione
    if ($file['size'] > MAX_FILE_SIZE) {
        http_response_code(400);
        echo json_encode(['error' => 'File troppo grande (max 5MB)']);
        exit;
    }
    
    // Verifica tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_TYPES)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo file non supportato. Usa: JPG, PNG, GIF, WebP']);
        exit;
    }
    
    // Verifica estensione
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        http_response_code(400);
        echo json_encode(['error' => 'Estensione file non valida']);
        exit;
    }
    
    // Crea cartella utente se non esiste
    $userUploadDir = UPLOAD_DIR . 'user_' . $userId . '/';
    if (!is_dir($userUploadDir)) {
        if (!mkdir($userUploadDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Impossibile creare cartella upload']);
            exit;
        }
    }
    
    // Genera nome file univoco
    // Formato: timestamp_random.extension
    $newFilename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $targetPath = $userUploadDir . $newFilename;
    $relativePath = 'uploads/user_' . $userId . '/' . $newFilename;
    
    // Sposta il file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Successo!
        echo json_encode([
            'success' => true,
            'image_path' => $relativePath,
            'image_url' => '/' . $relativePath,
            'message' => 'Immagine caricata con successo'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel salvataggio del file']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    /**
     * DELETE: Elimina un'immagine
     * 
     * Parametri (JSON body):
     * - image_path: percorso relativo dell'immagine da eliminare
     */
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['image_path'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Percorso immagine mancante']);
        exit;
    }
    
    $imagePath = $data['image_path'];
    
    // Sicurezza: verifica che il percorso sia nella cartella uploads dell'utente
    $expectedPrefix = 'uploads/user_' . $userId . '/';
    if (strpos($imagePath, $expectedPrefix) !== 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Accesso non autorizzato']);
        exit;
    }
    
    // Sicurezza: previeni path traversal
    if (strpos($imagePath, '..') !== false) {
        http_response_code(403);
        echo json_encode(['error' => 'Percorso non valido']);
        exit;
    }
    
    $fullPath = __DIR__ . '/../' . $imagePath;
    
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo json_encode([
                'success' => true,
                'message' => 'Immagine eliminata'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Errore eliminazione file']);
        }
    } else {
        // File non esiste, consideriamolo un successo
        echo json_encode([
            'success' => true,
            'message' => 'File già eliminato o non esistente'
        ]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito. Usa POST per upload, DELETE per eliminare.']);
}
