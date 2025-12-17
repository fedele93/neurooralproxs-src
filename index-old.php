<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuroOral Pro - Ripetizione Spaziata per Neurologia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --light: #f9fafb;
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .btn {
            background: rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-size: 14px;
        }

        .btn:hover {
            background: rgba(99, 102, 241, 0.3);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
            color: white;
        }

        .flashcard {
            perspective: 1000px;
            background: transparent;
            border-radius: 20px;
            margin: 30px auto;
            max-width: 700px;
            height: 450px;
            cursor: pointer;
            position: relative;
        }

        .flashcard-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .flashcard.flipped .flashcard-inner {
            transform: rotateY(180deg);
        }

        .flashcard-front, .flashcard-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: rgba(30, 27, 75, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .flashcard-back {
            transform: rotateY(180deg);
            background: rgba(45, 40, 100, 0.9);
        }

        .flashcard-content {
            max-height: 300px;
            overflow-y: auto;
            margin: 20px 0;
            line-height: 1.7;
        }

        .difficulty-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .diff-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .diff-easy {
            background: var(--success);
            color: white;
        }

        .diff-medium {
            background: var(--warning);
            color: white;
        }

        .diff-hard {
            background: var(--danger);
            color: white;
        }

        .diff-btn:hover {
            transform: scale(1.05);
        }

        .progress {
            background: rgba(255, 255, 255, 0.1);
            height: 10px;
            border-radius: 10px;
            overflow: hidden;
            margin: 20px 0;
        }

        .progress-fill {
            background: var(--gradient);
            height: 100%;
            transition: width 0.5s ease;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: rgba(30, 27, 75, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #a5b4fc;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
        }

        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(30, 27, 75, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            transform: translateY(150px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2000;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .session-complete {
            text-align: center;
            padding: 50px;
        }

        .session-complete h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .session-complete p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #cbd5e1;
        }

        /* Login Screen */
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: rgba(30, 27, 75, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }

            .flashcard {
                height: 350px;
            }

            .flashcard-front,
            .flashcard-back {
                padding: 25px;
            }

            .difficulty-buttons {
                flex-direction: column;
            }

            .diff-btn {
                width: 100%;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Login Screen -->
    <div id="loginScreen" style="display: none;">
        <div class="login-container">
            <h2>🧠 NeuroOral Pro</h2>
            
            <div class="alert alert-info" id="defaultCredsAlert">
                <strong>🔑 Primo accesso:</strong><br>
                Username: <code>admin</code><br>
                Password: <code>neurooralproxs</code>
            </div>

            <div class="alert alert-error" id="loginError" style="display: none;"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="loginUsername">Username</label>
                    <input type="text" id="loginUsername" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                    🔓 Accedi
                </button>
            </form>
        </div>
    </div>

    <!-- Main App -->
    <div id="mainApp" style="display: none;">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>🧠 NeuroOral Pro</h1>
                <p>Sistema di Ripetizione Spaziata per Neurologia Clinica</p>
                <div class="user-info">
                    <span>👤 <strong id="userFullName"></strong> (<span id="userName"></span>)</span>
                    <button class="btn btn-danger" onclick="logout()">🚪 Logout</button>
                </div>
            </div>

            <!-- Stats Dashboard -->
            <div class="stats">
                <div class="stat-card">
                    <h3 id="cardsStudied">0</h3>
                    <p>Carte Studiate</p>
                </div>
                <div class="stat-card">
                    <h3 id="dueToday">0</h3>
                    <p>Da Rivedere Oggi</p>
                </div>
                <div class="stat-card">
                    <h3 id="avgEF">2.5</h3>
                    <p>Ease Factor Medio</p>
                </div>
                <div class="stat-card">
                    <h3 id="totalReps">0</h3>
                    <p>Revisioni Totali</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress">
                <div class="progress-fill" id="progressBar"></div>
            </div>

            <!-- Controls -->
            <div class="controls">
                <button class="btn" onclick="loadStats()">🔄 Aggiorna Stats</button>
                <button class="btn" onclick="openAddModal()">➕ Aggiungi Carta</button>
                <button class="btn btn-primary" onclick="startSession()">▶️ Inizia Sessione</button>
            </div>

            <!-- Flashcard Container -->
            <div id="flashcardContainer" style="display: none;">
                <div style="text-align: center; margin: 20px 0; font-size: 1.1rem; color: #cbd5e1;">
                    Carta <span id="currentCard">1</span> di <span id="totalCards">0</span>
                </div>
                
                <div class="flashcard" id="currentFlashcard" onclick="flipFlashcard(event)">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <h2>❓ Domanda</h2>
                            <div class="flashcard-content" id="questionText"></div>
                            <p style="color: #94a3b8; margin-top: 20px; font-size: 0.9rem;">
                                Clicca per vedere la risposta
                            </p>
                        </div>
                        <div class="flashcard-back">
                            <h2>📖 Risposta</h2>
                            <div class="flashcard-content" id="answerText"></div>
                        </div>
                    </div>
                </div>

                <div style="text-align: center;">
                    <p style="color: #94a3b8; margin-bottom: 20px;">Valuta la tua risposta:</p>
                    <div class="difficulty-buttons">
                        <button class="diff-btn diff-easy" onclick="reviewCard('easy')">
                            ⭐ Risposta Perfetta
                        </button>
                        <button class="diff-btn diff-medium" onclick="reviewCard('medium')">
                            👍 Buona Risposta
                        </button>
                        <button class="diff-btn diff-hard" onclick="reviewCard('hard')">
                            📚 Da Ripassare
                        </button>
                    </div>
                </div>
            </div>

            <!-- Session Complete -->
            <div id="sessionComplete" style="display: none;">
                <div class="session-complete">
                    <h2>🎉 Sessione Completata!</h2>
                    <p id="sessionSummary"></p>
                    <button class="btn btn-primary" onclick="location.reload()">
                        Torna al Menu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Card Modal -->
    <div class="modal" id="addCardModal">
        <div class="modal-content">
            <h2>Aggiungi Nuova Flashcard</h2>
            
            <div class="form-group">
                <label for="cardCategory">Categoria</label>
                <input type="text" id="cardCategory" placeholder="es. Neurologia Clinica">
            </div>
            
            <div class="form-group">
                <label for="cardType">Tipo</label>
                <select id="cardType">
                    <option value="definition">Definizione/Fatto</option>
                    <option value="spiegazione">Spiegazione Strutturata</option>
                    <option value="differenziale">Differenziale</option>
                    <option value="dettaglio">Dettaglio Clinico</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cardQuestion">Domanda</label>
                <textarea id="cardQuestion" placeholder="Scrivi la domanda..." rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="cardAnswer">Risposta</label>
                <textarea id="cardAnswer" placeholder="Scrivi la risposta modello..." rows="6"></textarea>
            </div>
            
            <div class="controls">
                <button class="btn btn-primary" onclick="addCard()">✅ Salva Carta</button>
                <button class="btn" onclick="closeAddModal()">❌ Annulla</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        const API_BASE = '/api';
        let currentCards = [];
        let currentCardIndex = 0;
        let isFlipped = false;
        let currentUser = null;

        // ============================================
        // AUTHENTICATION
        // ============================================

        async function checkAuth() {
            try {
                const response = await fetch(API_BASE + '/auth');
                const data = await response.json();

                if (data.authenticated) {
                    currentUser = data.user;
                    showMainApp();
                } else {
                    showLoginScreen();
                }
            } catch (error) {
                console.error('Auth check failed:', error);
                showLoginScreen();
            }
        }

        function showLoginScreen() {
            document.getElementById('loginScreen').style.display = 'block';
            document.getElementById('mainApp').style.display = 'none';
        }

        function showMainApp() {
            document.getElementById('loginScreen').style.display = 'none';
            document.getElementById('mainApp').style.display = 'block';
            document.getElementById('userFullName').textContent = currentUser.full_name;
            document.getElementById('userName').textContent = currentUser.username;
            loadStats();
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('loginUsername').value;
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            
            try {
                const response = await fetch(API_BASE + '/auth', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({username, password})
                });

                const data = await response.json();

                if (data.success) {
                    currentUser = data.user;
                    document.getElementById('defaultCredsAlert').style.display = 'none';
                    showMainApp();
                } else {
                    errorDiv.textContent = data.error || 'Login fallito';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                errorDiv.textContent = 'Errore di connessione';
                errorDiv.style.display = 'block';
            }
        });

        async function logout() {
            try {
                await fetch(API_BASE + '/auth', {method: 'DELETE'});
                currentUser = null;
                showLoginScreen();
            } catch (error) {
                console.error('Logout failed:', error);
            }
        }

        // ============================================
        // UTILITY FUNCTIONS
        // ============================================

        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.borderTopColor = type === 'success' ? '#10b981' : '#6366f1';
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        async function apiCall(endpoint, method = 'GET', data = null) {
            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                };

                if (data) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(API_BASE + endpoint, options);
                
                if (response.status === 401) {
                    showToast('Sessione scaduta, rieffettua il login', 'error');
                    setTimeout(() => showLoginScreen(), 1500);
                    return null;
                }

                const json = await response.json();

                if (!response.ok) {
                    throw new Error(json.error || 'API Error');
                }

                return json;
            } catch (error) {
                showToast(`Errore: ${error.message}`, 'error');
                console.error(error);
                return null;
            }
        }

        // ============================================
        // STATISTICS
        // ============================================

        async function loadStats() {
            const stats = await apiCall('/stats');
            if (stats) {
                document.getElementById('cardsStudied').textContent = stats.studied_cards;
                document.getElementById('dueToday').textContent = stats.due_today;
                document.getElementById('avgEF').textContent = stats.average_ease_factor;
                document.getElementById('totalReps').textContent = stats.total_repetitions;
            }
        }

        // ============================================
        // STUDY SESSION
        // ============================================

        async function startSession() {
            currentCards = await apiCall('/cards?limit=50');
            
            if (!currentCards || currentCards.length === 0) {
                showToast('Nessuna carta da rivedere oggi!', 'info');
                return;
            }

            currentCardIndex = 0;
            isFlipped = false;

            document.getElementById('flashcardContainer').style.display = 'block';
            document.getElementById('sessionComplete').style.display = 'none';
            document.getElementById('totalCards').textContent = currentCards.length;

            showCard();
        }

        function showCard() {
            if (currentCardIndex >= currentCards.length) {
                showSessionComplete();
                return;
            }

            const card = currentCards[currentCardIndex];
            document.getElementById('questionText').textContent = card.question;
            document.getElementById('answerText').textContent = card.answer;
            document.getElementById('currentCard').textContent = currentCardIndex + 1;
            
            const flashcard = document.getElementById('currentFlashcard');
            flashcard.classList.remove('flipped');
            isFlipped = false;

            updateProgress();
        }

        function flipFlashcard(event) {
            const flashcard = document.getElementById('currentFlashcard');
            flashcard.classList.toggle('flipped');
            isFlipped = !isFlipped;
        }

        async function reviewCard(difficulty) {
            const card = currentCards[currentCardIndex];
            
            const result = await apiCall('/review', 'POST', {
                card_id: card.id,
                quality: difficulty
            });

            if (result && result.success) {
                showToast(`EF: ${result.new_ef.toFixed(2)} | Prossima: ${result.interval_days}gg`, 'success');
                
                currentCardIndex++;
                setTimeout(() => {
                    showCard();
                }, 500);
            }
        }

        function showSessionComplete() {
            document.getElementById('flashcardContainer').style.display = 'none';
            document.getElementById('sessionComplete').style.display = 'block';
            document.getElementById('sessionSummary').textContent = 
                `Hai riveduto ${currentCardIndex} carte! 🎓`;
            
            setTimeout(loadStats, 500);
        }

        function updateProgress() {
            const progress = currentCards.length > 0 
                ? (currentCardIndex / currentCards.length) * 100 
                : 0;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        // ============================================
        // CARD MANAGEMENT
        // ============================================

        function openAddModal() {
            document.getElementById('addCardModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addCardModal').classList.remove('active');
        }

        async function addCard() {
            const category = document.getElementById('cardCategory').value;
            const type = document.getElementById('cardType').value;
            const question = document.getElementById('cardQuestion').value;
            const answer = document.getElementById('cardAnswer').value;

            if (!category || !question || !answer) {
                showToast('Completa tutti i campi!', 'error');
                return;
            }

            const result = await apiCall('/cards', 'POST', {
                category, type, question, answer
            });

            if (result && result.success) {
                showToast('Carta aggiunta! ✅', 'success');
                document.getElementById('cardCategory').value = '';
                document.getElementById('cardQuestion').value = '';
                document.getElementById('cardAnswer').value = '';
                closeAddModal();
                loadStats();
            }
        }

        // ============================================
        // INITIALIZATION
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.code === 'Space' && currentCardIndex < currentCards.length) {
                event.preventDefault();
                flipFlashcard();
            }
        });
    </script>
</body>
</html>
