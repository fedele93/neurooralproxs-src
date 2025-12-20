<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuroOral Pro - SRS Neurologia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            margin: 0 auto;
        }
        
        h1 { 
            color: #667eea; 
            margin-bottom: 10px; 
            text-align: center; 
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        /* === USER SWITCHER === */
        .user-switcher {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .user-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .user-btn:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .user-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e0e5ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .user-btn.active .user-avatar {
            background: rgba(255,255,255,0.3);
        }
        
        /* === Streak Banner === */
        .streak-banner {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .streak-main {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .streak-fire { font-size: 40px; }
        .streak-info h2 { font-size: 24px; margin: 0; }
        .streak-info p { font-size: 13px; opacity: 0.9; margin: 0; }
        
        .streak-stats { display: flex; gap: 20px; }
        .streak-stat { text-align: center; }
        .streak-stat-value { font-size: 20px; font-weight: bold; }
        .streak-stat-label { font-size: 11px; opacity: 0.9; }
        
        /* === Daily Goal Progress === */
        .daily-goal-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .daily-goal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .daily-goal-header h4 { color: #667eea; margin: 0; font-size: 14px; }
        .daily-goal-count { font-size: 14px; color: #666; }
        
        .progress-bar {
            height: 12px;
            background: #e0e5ff;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 6px;
            transition: width 0.5s ease;
        }
        
        .progress-fill.complete {
            background: linear-gradient(90deg, #26de81, #20c770);
        }
        
        /* Dashboard statistiche */
        .stats-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-box .number { font-size: 28px; font-weight: bold; }
        .stat-box .label { font-size: 12px; opacity: 0.9; }
        
        .stat-box.leeches {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .stat-box.leeches:hover { transform: scale(1.05); }
        
        /* Insights box */
        .insights-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .insights-box h3 { color: #667eea; margin-bottom: 10px; font-size: 14px; }
        .insight-item { margin: 8px 0; color: #444; }
        
        /* Menu buttons */
        .menu-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
        }
        
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        button:hover { background: #5568d3; transform: translateY(-2px); }
        button.secondary { background: #e0e5ff; color: #667eea; }
        button.secondary:hover { background: #c7d0ff; }
        button.danger { background: #ff6b6b; }
        button.danger:hover { background: #ee5a5a; }
        button.warning { background: #feca57; color: #333; }
        button.warning:hover { background: #feb940; }
        button.small { padding: 8px 16px; font-size: 13px; }
        
        /* Flashcard per review */
        .flashcard {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
            min-height: 200px;
        }
        
        .flashcard.cloze-card { border-left-color: #9b59b6; }
        .flashcard-question { font-size: 18px; color: #333; margin-bottom: 20px; line-height: 1.6; }
        
        .flashcard-answer {
            font-size: 16px;
            color: #444;
            background: #e8ebff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            line-height: 1.6;
        }
        
        .flashcard-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            margin: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .flashcard-meta {
            font-size: 12px;
            color: #888;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        /* Cloze styles */
        .cloze-blank {
            background: #667eea;
            color: white;
            padding: 2px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 2px;
        }
        
        .cloze-revealed {
            background: #26de81;
            color: white;
            padding: 2px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 2px;
        }
        
        .cloze-type-badge {
            display: inline-block;
            background: #9b59b6;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 8px;
        }
        
        /* Rating buttons */
        .rating-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .rating-btn { padding: 15px 25px; border-radius: 12px; font-weight: bold; }
        .rating-btn.hard { background: #ff6b6b; }
        .rating-btn.hard:hover { background: #ee5a5a; }
        .rating-btn.good { background: #feca57; color: #333; }
        .rating-btn.good:hover { background: #feb940; }
        .rating-btn.easy { background: #26de81; }
        .rating-btn.easy:hover { background: #20c770; }
        
        /* Form styles */
        .form-section { margin: 20px 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #444; }
        
        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; }
        textarea { min-height: 100px; resize: vertical; }
        
        /* === IMAGE UPLOAD STYLES === */
        .image-upload-section {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .image-upload-section:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .image-upload-section.dragover {
            border-color: #667eea;
            background: #e8ebff;
        }
        
        .upload-icon { font-size: 40px; margin-bottom: 10px; }
        .upload-text { color: #666; font-size: 14px; margin-bottom: 10px; }
        .upload-or { color: #999; font-size: 12px; margin: 10px 0; }
        
        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .remove-image-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        
        .remove-image-btn:hover {
            background: #ee5a5a;
            transform: none;
        }
        
        /* Cloze help box */
        .cloze-help-box {
            background: #f3e8ff;
            border: 2px solid #9b59b6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .cloze-help-box h4 { color: #9b59b6; margin-bottom: 8px; }
        .cloze-help-box code { background: #e8d5f5; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .cloze-help-box .example { background: white; padding: 10px; border-radius: 6px; margin-top: 10px; border-left: 3px solid #9b59b6; }
        
        /* Card type tabs */
        .card-type-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        
        .card-type-tab {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }
        
        .card-type-tab:hover { border-color: #667eea; }
        .card-type-tab.active { background: #667eea; color: white; border-color: #667eea; }
        .card-type-tab.active.cloze { background: #9b59b6; border-color: #9b59b6; }
        
        /* Cards list */
        .cards-list { margin-top: 20px; }
        
        .card-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }
        
        .card-item.leech { border-left-color: #ff6b6b; background: #fff5f5; }
        .card-item.cloze { border-left-color: #9b59b6; }
        
        .card-item-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; }
        .card-item-content { flex: 1; }
        .card-item-question { font-weight: 600; color: #333; margin-bottom: 5px; }
        .card-item-answer { font-size: 13px; color: #666; margin-bottom: 8px; max-height: 60px; overflow: hidden; }
        .card-item-meta { font-size: 11px; color: #888; }
        .card-item-actions { display: flex; gap: 8px; flex-shrink: 0; }
        .card-item-image { max-width: 80px; max-height: 60px; border-radius: 5px; margin-right: 10px; }
        
        /* Filter bar */
        .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .filter-bar select { width: auto; min-width: 200px; }
        .filter-bar input { flex: 1; min-width: 200px; }
        
        /* Category stats */
        .category-stats { margin-top: 20px; }
        
        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .category-name { font-weight: 600; color: #333; }
        .category-stats-mini { font-size: 12px; color: #666; }
        .category-ef { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .ef-high { background: #d4edda; color: #155724; }
        .ef-medium { background: #fff3cd; color: #856404; }
        .ef-low { background: #f8d7da; color: #721c24; }
        
        /* Leech info box */
        .leech-info-box {
            background: #fff5f5;
            border: 2px solid #ff6b6b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .leech-info-box h3 { color: #ff6b6b; margin-bottom: 10px; }
        .leech-info-box ul { margin-left: 20px; color: #666; font-size: 14px; }
        .leech-info-box li { margin: 5px 0; }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .modal.show { display: flex; }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h2 { color: #667eea; margin: 0; }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            padding: 0;
        }
        
        .modal-close:hover { color: #333; background: none; transform: none; }
        
        /* Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s;
            z-index: 2000;
            max-width: 350px;
        }
        
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #26de81; }
        .toast.warning { background: #feca57; color: #333; }
        .toast.error { background: #ff6b6b; }
        .toast.leech { background: #8b0000; }
        .toast.celebration { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        
        /* Utility */
        .hidden { display: none !important; }
        .text-center { text-align: center; }
        .mb-20 { margin-bottom: 20px; }
        
        /* Badges */
        .leech-badge {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 8px;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .container { padding: 20px; }
            .stats-dashboard { grid-template-columns: repeat(2, 1fr); }
            .rating-buttons { flex-direction: column; }
            .rating-btn { width: 100%; }
            .card-item-header { flex-direction: column; }
            .card-item-actions { margin-top: 10px; }
            .filter-bar { flex-direction: column; }
            .filter-bar select, .filter-bar input { width: 100%; }
            .card-type-tabs { flex-direction: column; }
            .streak-banner { flex-direction: column; text-align: center; }
            .streak-stats { justify-content: center; }
            .user-switcher { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧠 NeuroOral Pro</h1>
        <p class="subtitle" id="appSubtitle">Sistema di Ripetizione Spaziata</p>
        
        <!-- === USER SWITCHER === -->
        <div class="user-switcher" id="userSwitcher">
            <!-- Popolato dinamicamente -->
        </div>
        
        <!-- === Streak Banner === -->
        <div class="streak-banner" id="streakBanner">
            <div class="streak-main">
                <div class="streak-fire" id="streakEmoji">🔥</div>
                <div class="streak-info">
                    <h2><span id="streakCount">0</span> giorni</h2>
                    <p id="streakMessage">Inizia a studiare per creare una streak!</p>
                </div>
            </div>
            <div class="streak-stats">
                <div class="streak-stat">
                    <div class="streak-stat-value" id="bestStreak">0</div>
                    <div class="streak-stat-label">Record</div>
                </div>
                <div class="streak-stat">
                    <div class="streak-stat-value" id="totalDays">0</div>
                    <div class="streak-stat-label">Giorni totali</div>
                </div>
            </div>
        </div>
        
        <!-- === Daily Goal Progress === -->
        <div class="daily-goal-section" id="dailyGoalSection">
            <div class="daily-goal-header">
                <h4>🎯 Obiettivo Giornaliero</h4>
                <span class="daily-goal-count"><span id="reviewedToday">0</span> / <span id="dailyGoal">20</span> carte</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
        </div>
        
        <!-- Dashboard statistiche -->
        <div class="stats-dashboard" id="statsDashboard">
            <div class="stat-box">
                <div class="number" id="statTotal">-</div>
                <div class="label">Totale Carte</div>
            </div>
            <div class="stat-box">
                <div class="number" id="statDue">-</div>
                <div class="label">Da Ripassare</div>
            </div>
            <div class="stat-box">
                <div class="number" id="statNew">-</div>
                <div class="label">Nuove</div>
            </div>
            <div class="stat-box">
                <div class="number" id="statEF">-</div>
                <div class="label">EF Medio</div>
            </div>
            <div class="stat-box leeches" id="statLeechesBox" onclick="showLeeches()" title="Clicca per gestire le carte sospese">
                <div class="number" id="statLeeches">0</div>
                <div class="label">🧛 Leeches</div>
            </div>
        </div>
        
        <!-- Box insights -->
        <div class="insights-box" id="insightsBox">
            <h3>💡 Suggerimenti</h3>
            <div id="insightsList">Caricamento...</div>
        </div>
        
        <!-- Menu principale -->
        <div id="menuSection">
            <div class="menu-buttons">
                <button onclick="startReview()">📚 Inizia Ripasso</button>
                <button onclick="showAddCard()" class="secondary">➕ Aggiungi Carta</button>
                <button onclick="showManageCards()" class="secondary">✏️ Gestisci Carte</button>
                <button onclick="showCategoryStats()" class="secondary">📊 Statistiche</button>
            </div>
        </div>
        
        <!-- Sezione Review -->
        <div id="reviewSection" class="hidden">
            <div class="text-center mb-20">
                <span id="reviewProgress">Carta 1 di ?</span>
            </div>
            
            <div class="flashcard" id="flashcard">
                <div class="flashcard-question" id="cardQuestion"></div>
                <div id="cardImageContainer" class="hidden">
                    <img id="cardImage" class="flashcard-image" src="" alt="Immagine">
                </div>
                <div id="cardAnswerSection" class="hidden">
                    <div class="flashcard-answer" id="cardAnswer"></div>
                </div>
                <div class="flashcard-meta" id="cardMeta"></div>
            </div>
            
            <div class="text-center">
                <button id="showAnswerBtn" onclick="showAnswer()">👁️ Mostra Risposta</button>
                
                <div id="ratingSection" class="hidden">
                    <p class="mb-20" style="color: #666;">Come hai risposto?</p>
                    <div class="rating-buttons">
                        <button class="rating-btn hard" onclick="rateCard(1)">❌ Difficile</button>
                        <button class="rating-btn good" onclick="rateCard(3)">✓ Buona</button>
                        <button class="rating-btn easy" onclick="rateCard(5)">⭐ Facile</button>
                    </div>
                </div>
            </div>
            
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="endReview()" class="secondary">← Torna al Menu</button>
            </div>
        </div>
        
        <!-- Sezione Aggiungi Carta -->
        <div id="addCardSection" class="hidden">
            <h2 style="margin-bottom: 20px;">➕ Aggiungi Nuova Flashcard</h2>
            
            <div class="card-type-tabs">
                <div class="card-type-tab active" id="tabBasic" onclick="switchCardType('basic')">📝 Carta Base</div>
                <div class="card-type-tab cloze" id="tabCloze" onclick="switchCardType('cloze')">🧩 Cloze</div>
            </div>
            
            <div class="cloze-help-box hidden" id="clozeHelpBox">
                <h4>🧩 Come creare carte Cloze</h4>
                <p>Scrivi il testo e metti tra <code>{{doppie graffe}}</code> le parole da nascondere:</p>
                <div class="example">
                    <strong>Scrivi:</strong> La sindrome di {{Wallenberg}} è causata da occlusione della {{PICA}}.<br>
                    <strong>Vedrai:</strong> La sindrome di <span class="cloze-blank">[...]</span> è causata da occlusione della <span class="cloze-blank">[...]</span>.
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-group">
                    <label for="newCategory">Categoria</label>
                    <select id="newCategory">
                        <option value="Neuroanatomia">Neuroanatomia</option>
                        <option value="Neurofisiologia">Neurofisiologia</option>
                        <option value="Patologie Degenerative">Patologie Degenerative</option>
                        <option value="Patologie Vascolari">Patologie Vascolari</option>
                        <option value="Patologie Infiammatorie">Patologie Infiammatorie</option>
                        <option value="Neuro-oncologia">Neuro-oncologia</option>
                        <option value="Epilessia">Epilessia</option>
                        <option value="Cefalee">Cefalee</option>
                        <option value="Neuropatie">Neuropatie</option>
                        <option value="Miopatie">Miopatie</option>
                        <option value="Neuroimaging">Neuroimaging</option>
                        <option value="Farmacologia">Farmacologia</option>
                        <option value="Altro">Altro</option>
                    </select>
                </div>
                
                <div id="basicCardFields">
                    <div class="form-group">
                        <label for="newQuestion">Domanda</label>
                        <textarea id="newQuestion" placeholder="Es: Quali sono le caratteristiche della sindrome di Wallenberg?"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="newAnswer">Risposta</label>
                        <textarea id="newAnswer" placeholder="Scrivi la risposta..." rows="6"></textarea>
                    </div>
                </div>
                
                <div id="clozeCardFields" class="hidden">
                    <div class="form-group">
                        <label for="newClozeText">Testo con Cloze</label>
                        <textarea id="newClozeText" placeholder="Es: La sindrome di {{Wallenberg}} è causata da occlusione della {{PICA}}." rows="6"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Anteprima</label>
                        <div id="clozePreview" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <span style="color: #888;">L'anteprima apparirà qui...</span>
                        </div>
                    </div>
                </div>
                
                <!-- === IMAGE UPLOAD SECTION === -->
                <div class="form-group">
                    <label>Immagine (opzionale)</label>
                    <div class="image-upload-section" id="imageUploadSection" 
                         ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)" 
                         ondrop="handleDrop(event)">
                        <div class="upload-icon">📷</div>
                        <div class="upload-text">Trascina un'immagine qui</div>
                        <div class="upload-or">oppure</div>
                        <input type="file" id="newImageFile" accept="image/*" onchange="handleFileSelect(event)" style="display: none;">
                        <button type="button" class="secondary small" onclick="document.getElementById('newImageFile').click()">
                            📁 Scegli file
                        </button>
                        <div id="newImagePreview" class="hidden">
                            <div class="image-preview-container">
                                <img id="newImagePreviewImg" class="image-preview" src="">
                                <button type="button" class="remove-image-btn" onclick="removeNewImage()">×</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="newImagePath" value="">
                </div>
                
                <div class="form-group">
                    <label for="newImageUrl">...oppure URL Immagine esterna</label>
                    <input type="text" id="newImageUrl" placeholder="https://esempio.com/immagine.jpg">
                </div>
            </div>
            
            <div class="menu-buttons">
                <button onclick="saveCard()">💾 Salva Carta</button>
                <button onclick="showMenu()" class="secondary">← Annulla</button>
            </div>
        </div>
        
        <!-- Sezione Gestisci Carte -->
        <div id="manageCardsSection" class="hidden">
            <h2 style="margin-bottom: 20px;">✏️ Gestisci Carte</h2>
            <div class="filter-bar">
                <select id="filterCategory" onchange="filterCards()"><option value="">Tutte le categorie</option></select>
                <input type="text" id="filterSearch" placeholder="Cerca..." oninput="filterCards()">
            </div>
            <div class="cards-list" id="cardsList">Caricamento...</div>
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="showMenu()" class="secondary">← Torna al Menu</button>
            </div>
        </div>
        
        <!-- Sezione Leeches -->
        <div id="leechesSection" class="hidden">
            <h2 style="margin-bottom: 20px;">🧛 Gestione Leeches</h2>
            <div class="leech-info-box">
                <h3>Cosa sono le Leeches?</h3>
                <p>Carte con 4+ fallimenti. Prima di riabilitarle:</p>
                <ul>
                    <li>Dividi in carte più piccole</li>
                    <li>Aggiungi un'immagine</li>
                    <li>Trasforma in Cloze</li>
                </ul>
            </div>
            <div class="cards-list" id="leechesList">Caricamento...</div>
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="showMenu()" class="secondary">← Torna al Menu</button>
            </div>
        </div>
        
        <!-- Sezione Statistiche -->
        <div id="statsSection" class="hidden">
            <h2 style="margin-bottom: 20px;">📊 Statistiche</h2>
            <div class="category-stats" id="categoryStatsList">Caricamento...</div>
            <h3 style="margin: 30px 0 15px 0;">⚠️ Carte a Rischio</h3>
            <div id="problematicCardsList">Caricamento...</div>
            <div class="text-center" style="margin-top: 30px;">
                <button onclick="showMenu()" class="secondary">← Torna al Menu</button>
            </div>
        </div>
        
        <!-- Sessione completata -->
        <div id="completedSection" class="hidden">
            <div class="text-center" style="padding: 40px 0;">
                <h2 style="color: #26de81; margin-bottom: 20px;" id="completedTitle">🎉 Sessione Completata!</h2>
                <p style="color: #666; margin-bottom: 30px;" id="sessionSummary"></p>
                <button onclick="showMenu()">← Torna al Menu</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Modifica -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Modifica Carta</h2>
                <button class="modal-close" onclick="closeEditModal()">×</button>
            </div>
            <input type="hidden" id="editCardId">
            <input type="hidden" id="editImagePath">
            <div class="form-group">
                <label for="editCategory">Categoria</label>
                <select id="editCategory">
                    <option value="Neuroanatomia">Neuroanatomia</option>
                    <option value="Neurofisiologia">Neurofisiologia</option>
                    <option value="Patologie Degenerative">Patologie Degenerative</option>
                    <option value="Patologie Vascolari">Patologie Vascolari</option>
                    <option value="Patologie Infiammatorie">Patologie Infiammatorie</option>
                    <option value="Neuro-oncologia">Neuro-oncologia</option>
                    <option value="Epilessia">Epilessia</option>
                    <option value="Cefalee">Cefalee</option>
                    <option value="Neuropatie">Neuropatie</option>
                    <option value="Miopatie">Miopatie</option>
                    <option value="Neuroimaging">Neuroimaging</option>
                    <option value="Farmacologia">Farmacologia</option>
                    <option value="Altro">Altro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editQuestion">Domanda</label>
                <textarea id="editQuestion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="editAnswer">Risposta</label>
                <textarea id="editAnswer" rows="6"></textarea>
            </div>
            
            <!-- Image Upload in Modal -->
            <div class="form-group">
                <label>Immagine</label>
                <div class="image-upload-section" id="editImageUploadSection"
                     ondragover="handleDragOver(event, 'edit')" 
                     ondragleave="handleDragLeave(event, 'edit')" 
                     ondrop="handleDrop(event, 'edit')">
                    <div class="upload-icon">📷</div>
                    <div class="upload-text">Trascina un'immagine qui</div>
                    <div class="upload-or">oppure</div>
                    <input type="file" id="editImageFile" accept="image/*" onchange="handleFileSelect(event, 'edit')" style="display: none;">
                    <button type="button" class="secondary small" onclick="document.getElementById('editImageFile').click()">
                        📁 Scegli file
                    </button>
                    <div id="editImagePreview" class="hidden">
                        <div class="image-preview-container">
                            <img id="editImagePreviewImg" class="image-preview" src="">
                            <button type="button" class="remove-image-btn" onclick="removeEditImage()">×</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="editImageUrl">...oppure URL Immagine esterna</label>
                <input type="text" id="editImageUrl">
            </div>
            
            <div class="menu-buttons">
                <button onclick="saveEditCard()">💾 Salva</button>
                <button onclick="closeEditModal()" class="secondary">Annulla</button>
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>

    <script>
        let currentCard = null;
        let reviewedCount = 0;
        let remainingCards = 0;
        let allCards = [];
        let currentCardType = 'basic';
        let dailyGoal = 20;
        let sessionReviewedToday = 0;
        let currentUserId = 1;
        let users = [];
        
        // === CATEGORIE PER UTENTE ===
        const categoriesByUser = {
            1: [ // Neurologia
                'Neuroanatomia',
                'Neurofisiologia',
                'Patologie Degenerative',
                'Patologie Vascolari',
                'Patologie Infiammatorie',
                'Neuro-oncologia',
                'Epilessia',
                'Cefalee',
                'Neuropatie',
                'Miopatie',
                'Neuroimaging',
                'Farmacologia',
                'Altro'
            ],
            2: [ // Medicina Legale
                'Tanatologia',
                'Traumatologia Forense',
                'Tossicologia Forense',
                'Asfissiologia',
                'Identificazione Personale',
                'Responsabilità Professionale',
                'Medicina Legale Penale',
                'Medicina Legale Civile',
                'Psicopatologia Forense',
                'Sessuologia Forense',
                'Genetica Forense',
                'Altro'
            ]
        };
        
        // Popola i dropdown delle categorie in base all'utente
        function updateCategoryDropdowns() {
            const categories = categoriesByUser[currentUserId] || categoriesByUser[1];
            const optionsHtml = categories.map(cat => 
                `<option value="${cat}">${cat}</option>`
            ).join('');
            
            // Aggiorna dropdown creazione
            const newCatSelect = document.getElementById('newCategory');
            if (newCatSelect) newCatSelect.innerHTML = optionsHtml;
            
            // Aggiorna dropdown modifica
            const editCatSelect = document.getElementById('editCategory');
            if (editCatSelect) editCatSelect.innerHTML = optionsHtml;
            
            // Aggiorna subtitle
            const subtitle = document.getElementById('appSubtitle');
            if (subtitle) {
                subtitle.textContent = currentUserId === 2 
                    ? 'Sistema di Ripetizione Spaziata per Medicina Legale' 
                    : 'Sistema di Ripetizione Spaziata per Neurologia';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            document.getElementById('newClozeText').addEventListener('input', updateClozePreview);
        });
        
        // === USER MANAGEMENT ===
        async function loadUsers() {
            try {
                const response = await fetch('/api/auth.php');
                const data = await response.json();
                users = data.users || [];
                currentUserId = data.current_user?.id || 1;
                renderUserSwitcher();
                updateCategoryDropdowns();
                loadStats();
            } catch (error) {
                console.error('Errore caricamento utenti:', error);
                updateCategoryDropdowns();
                loadStats();
            }
        }
        
        function renderUserSwitcher() {
            const container = document.getElementById('userSwitcher');
            container.innerHTML = users.map(user => `
                <button class="user-btn ${user.id === currentUserId ? 'active' : ''}" 
                        onclick="switchUser(${user.id})">
                    <div class="user-avatar">${user.display_name.charAt(0).toUpperCase()}</div>
                    ${user.display_name}
                </button>
            `).join('');
        }
        
        async function switchUser(userId) {
            if (userId === currentUserId) return;
            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                });
                const data = await response.json();
                if (data.success) {
                    currentUserId = userId;
                    renderUserSwitcher();
                    updateCategoryDropdowns();
                    showToast(`Ciao ${data.user.display_name}!`, 'success');
                    showMenu();
                }
            } catch (error) {
                showToast('Errore cambio utente', 'error');
            }
        }
        
        // === CLOZE FUNCTIONS ===
        function isClozeCard(text) { return /\{\{.+?\}\}/.test(text); }
        function countClozes(text) { const m = text.match(/\{\{.+?\}\}/g); return m ? m.length : 0; }
        function clozeToQuestion(text) { return text.replace(/\{\{(.+?)\}\}/g, '<span class="cloze-blank">[...]</span>'); }
        function clozeToAnswer(text) { return text.replace(/\{\{(.+?)\}\}/g, '<span class="cloze-revealed">$1</span>'); }
        function extractClozeAnswers(text) {
            const m = text.match(/\{\{(.+?)\}\}/g);
            return m ? m.map(x => x.replace(/\{\{|\}\}/g, '')) : [];
        }
        
        function updateClozePreview() {
            const text = document.getElementById('newClozeText').value;
            const preview = document.getElementById('clozePreview');
            if (!text.trim()) {
                preview.innerHTML = '<span style="color: #888;">L\'anteprima apparirà qui...</span>';
                return;
            }
            const num = countClozes(text);
            if (num === 0) {
                preview.innerHTML = '<span style="color: #ff6b6b;">⚠️ Aggiungi almeno una {{parola}}</span>';
                return;
            }
            preview.innerHTML = `<div style="margin-bottom: 10px;"><strong>Domanda:</strong></div>
                <div>${clozeToQuestion(text)}</div>
                <div style="color: #26de81; font-size: 12px; margin-top: 10px;">✓ ${num} campo/i</div>`;
        }
        
        function switchCardType(type) {
            currentCardType = type;
            document.getElementById('tabBasic').classList.toggle('active', type === 'basic');
            document.getElementById('tabCloze').classList.toggle('active', type === 'cloze');
            document.getElementById('basicCardFields').classList.toggle('hidden', type === 'cloze');
            document.getElementById('clozeCardFields').classList.toggle('hidden', type === 'basic');
            document.getElementById('clozeHelpBox').classList.toggle('hidden', type === 'basic');
        }
        
        // === IMAGE UPLOAD FUNCTIONS ===
        function handleDragOver(event, context = 'new') {
            event.preventDefault();
            const section = context === 'edit' ? 'editImageUploadSection' : 'imageUploadSection';
            document.getElementById(section).classList.add('dragover');
        }
        
        function handleDragLeave(event, context = 'new') {
            event.preventDefault();
            const section = context === 'edit' ? 'editImageUploadSection' : 'imageUploadSection';
            document.getElementById(section).classList.remove('dragover');
        }
        
        function handleDrop(event, context = 'new') {
            event.preventDefault();
            const section = context === 'edit' ? 'editImageUploadSection' : 'imageUploadSection';
            document.getElementById(section).classList.remove('dragover');
            
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                uploadImage(files[0], context);
            }
        }
        
        function handleFileSelect(event, context = 'new') {
            const file = event.target.files[0];
            if (file) {
                uploadImage(file, context);
            }
        }
        
        async function uploadImage(file, context = 'new') {
            // Verifica tipo file
            if (!file.type.startsWith('image/')) {
                showToast('Seleziona un\'immagine valida', 'error');
                return;
            }
            
            // Verifica dimensione (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Immagine troppo grande (max 5MB)', 'error');
                return;
            }
            
            showToast('Caricamento...', 'info');
            
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('/api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (context === 'edit') {
                        document.getElementById('editImagePath').value = data.image_path;
                        document.getElementById('editImagePreviewImg').src = data.image_url;
                        document.getElementById('editImagePreview').classList.remove('hidden');
                        document.getElementById('editImageUrl').value = '';
                    } else {
                        document.getElementById('newImagePath').value = data.image_path;
                        document.getElementById('newImagePreviewImg').src = data.image_url;
                        document.getElementById('newImagePreview').classList.remove('hidden');
                        document.getElementById('newImageUrl').value = '';
                    }
                    showToast('Immagine caricata!', 'success');
                } else {
                    showToast(data.error || 'Errore upload', 'error');
                }
            } catch (error) {
                showToast('Errore di connessione', 'error');
            }
        }
        
        function removeNewImage() {
            document.getElementById('newImagePath').value = '';
            document.getElementById('newImagePreview').classList.add('hidden');
            document.getElementById('newImageFile').value = '';
        }
        
        function removeEditImage() {
            document.getElementById('editImagePath').value = '';
            document.getElementById('editImagePreview').classList.add('hidden');
            document.getElementById('editImageFile').value = '';
        }
        
        // === NAVIGATION ===
        function showMenu() {
            hideAllSections();
            document.getElementById('menuSection').classList.remove('hidden');
            document.getElementById('statsDashboard').classList.remove('hidden');
            document.getElementById('insightsBox').classList.remove('hidden');
            document.getElementById('streakBanner').classList.remove('hidden');
            document.getElementById('dailyGoalSection').classList.remove('hidden');
            document.getElementById('userSwitcher').classList.remove('hidden');
            loadStats();
        }
        
        function hideAllSections() {
            ['menuSection', 'reviewSection', 'addCardSection', 'manageCardsSection', 
             'statsSection', 'completedSection', 'leechesSection'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            document.getElementById('streakBanner').classList.add('hidden');
            document.getElementById('dailyGoalSection').classList.add('hidden');
            document.getElementById('userSwitcher').classList.add('hidden');
        }
        
        function showAddCard() {
            hideAllSections();
            document.getElementById('addCardSection').classList.remove('hidden');
            document.getElementById('newQuestion').value = '';
            document.getElementById('newAnswer').value = '';
            document.getElementById('newClozeText').value = '';
            document.getElementById('newImageUrl').value = '';
            document.getElementById('newImagePath').value = '';
            document.getElementById('newImagePreview').classList.add('hidden');
            document.getElementById('clozePreview').innerHTML = '<span style="color: #888;">L\'anteprima apparirà qui...</span>';
            switchCardType('basic');
        }
        
        function showManageCards() {
            hideAllSections();
            document.getElementById('manageCardsSection').classList.remove('hidden');
            loadAllCards();
        }
        
        function showCategoryStats() {
            hideAllSections();
            document.getElementById('statsSection').classList.remove('hidden');
            loadDetailedStats();
        }
        
        function showLeeches() {
            hideAllSections();
            document.getElementById('leechesSection').classList.remove('hidden');
            loadLeeches();
        }
        
        // === STATS ===
        async function loadStats() {
            try {
                const response = await fetch('/api/stats.php');
                const data = await response.json();
                
                document.getElementById('statTotal').textContent = data.total || 0;
                document.getElementById('statDue').textContent = data.due || 0;
                document.getElementById('statNew').textContent = data.new || 0;
                document.getElementById('statEF').textContent = data.avg_ef ? data.avg_ef.toFixed(2) : '2.50';
                
                const leechCount = data.leeches || 0;
                document.getElementById('statLeeches').textContent = leechCount;
                document.getElementById('statLeechesBox').style.display = leechCount === 0 ? 'none' : 'block';
                
                // Streak
                if (data.streak) {
                    const streak = data.streak;
                    document.getElementById('streakCount').textContent = streak.current;
                    document.getElementById('bestStreak').textContent = streak.best;
                    document.getElementById('totalDays').textContent = streak.total_days;
                    dailyGoal = streak.daily_goal;
                    
                    if (streak.current >= 7) {
                        document.getElementById('streakEmoji').textContent = '🔥';
                        document.getElementById('streakMessage').textContent = 'Sei in fuoco! Continua così!';
                    } else if (streak.current >= 3) {
                        document.getElementById('streakEmoji').textContent = '🔥';
                        document.getElementById('streakMessage').textContent = 'Ottima streak! Non fermarti!';
                    } else if (streak.current >= 1) {
                        document.getElementById('streakEmoji').textContent = '✨';
                        document.getElementById('streakMessage').textContent = 'Buon inizio! Costruisci la tua streak!';
                    } else {
                        document.getElementById('streakEmoji').textContent = '📚';
                        document.getElementById('streakMessage').textContent = 'Studia oggi per iniziare!';
                    }
                    
                    sessionReviewedToday = data.reviewed_today || 0;
                    document.getElementById('reviewedToday').textContent = sessionReviewedToday;
                    document.getElementById('dailyGoal').textContent = dailyGoal;
                    const progress = Math.min(100, (sessionReviewedToday / dailyGoal) * 100);
                    const progressFill = document.getElementById('progressFill');
                    progressFill.style.width = progress + '%';
                    progressFill.classList.toggle('complete', progress >= 100);
                }
                
                // Insights
                if (data.insights && data.insights.length > 0) {
                    document.getElementById('insightsList').innerHTML = 
                        data.insights.map(i => `<div class="insight-item">${i}</div>`).join('');
                } else {
                    document.getElementById('insightsList').innerHTML = '<div class="insight-item">✅ Tutto bene!</div>';
                }
                
            } catch (error) {
                console.error('Errore:', error);
            }
        }
        
        async function loadLeeches() {
            try {
                const response = await fetch('/api/leeches.php');
                const data = await response.json();
                if (data.leeches && data.leeches.length > 0) {
                    document.getElementById('leechesList').innerHTML = data.leeches.map(card => {
                        const isCloze = isClozeCard(card.question);
                        return `<div class="card-item leech">
                            <div class="card-item-header">
                                <div class="card-item-content">
                                    <div class="card-item-question">${escapeHtml(card.question)}
                                        <span class="leech-badge">${card.lapses} fail</span>
                                        ${isCloze ? '<span class="cloze-type-badge">Cloze</span>' : ''}
                                    </div>
                                    <div class="card-item-meta">${card.category || 'N/A'} | EF: ${parseFloat(card.easiness_factor).toFixed(2)}</div>
                                </div>
                                <div class="card-item-actions">
                                    <button class="small secondary" onclick="openEditModal(${card.id})">✏️</button>
                                    <button class="small warning" onclick="reactivateLeech(${card.id})">🔄</button>
                                    <button class="small danger" onclick="deleteLeech(${card.id})">🗑️</button>
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                } else {
                    document.getElementById('leechesList').innerHTML = '<p style="text-align:center;color:#26de81;padding:30px;">✅ Nessuna leech!</p>';
                }
            } catch (error) {
                document.getElementById('leechesList').innerHTML = '<p>Errore.</p>';
            }
        }
        
        async function reactivateLeech(cardId) {
            if (!confirm('Riattivare questa carta?')) return;
            try {
                const response = await fetch('/api/leeches.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reactivate', card_id: cardId })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Carta riattivata!', 'success');
                    loadLeeches();
                    loadStats();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        async function deleteLeech(cardId) {
            if (!confirm('Eliminare definitivamente?')) return;
            try {
                const response = await fetch('/api/leeches.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', card_id: cardId })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Eliminata', 'success');
                    loadLeeches();
                    loadStats();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        async function loadDetailedStats() {
            try {
                const response = await fetch('/api/stats.php');
                const data = await response.json();
                if (data.by_category && data.by_category.length > 0) {
                    document.getElementById('categoryStatsList').innerHTML = data.by_category.map(cat => {
                        const efClass = cat.avg_ef >= 2.3 ? 'ef-high' : (cat.avg_ef >= 2.0 ? 'ef-medium' : 'ef-low');
                        return `<div class="category-item">
                            <div>
                                <div class="category-name">${cat.category}</div>
                                <div class="category-stats-mini">${cat.total} carte | ${cat.due} da ripassare</div>
                            </div>
                            <span class="category-ef ${efClass}">EF: ${cat.avg_ef || 'N/A'}</span>
                        </div>`;
                    }).join('');
                }
                if (data.problematic_cards) {
                    const active = data.problematic_cards.filter(c => !c.suspended);
                    document.getElementById('problematicCardsList').innerHTML = active.length > 0 ?
                        active.map(c => `<div class="category-item">
                            <div class="category-name">${c.question.substring(0,50)}...</div>
                            <span class="category-ef ef-low">${c.lapses} fail</span>
                        </div>`).join('') : '<p style="color:#26de81;">✅ Nessuna!</p>';
                }
            } catch (error) { console.error(error); }
        }
        
        // === CARDS ===
        async function loadAllCards() {
            try {
                const response = await fetch('/api/cards.php');
                const data = await response.json();
                allCards = data.cards || [];
                const categories = [...new Set(allCards.map(c => c.category).filter(Boolean))];
                document.getElementById('filterCategory').innerHTML = '<option value="">Tutte</option>' +
                    categories.map(c => `<option value="${c}">${c}</option>`).join('');
                renderCardsList(allCards);
            } catch (error) {
                document.getElementById('cardsList').innerHTML = '<p>Errore.</p>';
            }
        }
        
        function filterCards() {
            const cat = document.getElementById('filterCategory').value;
            const search = document.getElementById('filterSearch').value.toLowerCase();
            let filtered = allCards;
            if (cat) filtered = filtered.filter(c => c.category === cat);
            if (search) filtered = filtered.filter(c => c.question.toLowerCase().includes(search) || c.answer.toLowerCase().includes(search));
            renderCardsList(filtered);
        }
        
        function renderCardsList(cards) {
            if (cards.length === 0) {
                document.getElementById('cardsList').innerHTML = '<p>Nessuna carta.</p>';
                return;
            }
            document.getElementById('cardsList').innerHTML = cards.map(card => {
                const isSuspended = card.suspended == 1;
                const isCloze = isClozeCard(card.question);
                let cls = 'card-item';
                if (isSuspended) cls += ' leech';
                else if (isCloze) cls += ' cloze';
                
                const imgHtml = card.effective_image ? 
                    `<img src="${card.effective_image}" class="card-item-image" alt="">` : '';
                
                return `<div class="${cls}">
                    <div class="card-item-header">
                        ${imgHtml}
                        <div class="card-item-content">
                            <div class="card-item-question">${escapeHtml(card.question)}
                                ${isSuspended ? '<span class="leech-badge">Sospesa</span>' : ''}
                                ${isCloze ? '<span class="cloze-type-badge">Cloze</span>' : ''}
                            </div>
                            <div class="card-item-meta">${card.category || 'N/A'} | EF: ${parseFloat(card.easiness_factor).toFixed(2)}</div>
                        </div>
                        <div class="card-item-actions">
                            <button class="small secondary" onclick="openEditModal(${card.id})">✏️</button>
                            ${isSuspended ? `<button class="small warning" onclick="reactivateLeech(${card.id})">🔄</button>` : ''}
                            <button class="small danger" onclick="deleteCard(${card.id})">🗑️</button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }
        
        // === EDIT MODAL ===
        async function openEditModal(cardId) {
            try {
                const response = await fetch(`/api/edit.php?id=${cardId}`);
                const data = await response.json();
                if (data.success && data.card) {
                    document.getElementById('editCardId').value = data.card.id;
                    document.getElementById('editCategory').value = data.card.category || 'Altro';
                    document.getElementById('editQuestion').value = data.card.question;
                    document.getElementById('editAnswer').value = data.card.answer;
                    document.getElementById('editImageUrl').value = data.card.image_url || '';
                    document.getElementById('editImagePath').value = data.card.image_path || '';
                    
                    if (data.card.effective_image) {
                        document.getElementById('editImagePreviewImg').src = data.card.effective_image;
                        document.getElementById('editImagePreview').classList.remove('hidden');
                    } else {
                        document.getElementById('editImagePreview').classList.add('hidden');
                    }
                    document.getElementById('editModal').classList.add('show');
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        function closeEditModal() { document.getElementById('editModal').classList.remove('show'); }
        
        async function saveEditCard() {
            const id = document.getElementById('editCardId').value;
            const category = document.getElementById('editCategory').value;
            const question = document.getElementById('editQuestion').value.trim();
            const answer = document.getElementById('editAnswer').value.trim();
            const imageUrl = document.getElementById('editImageUrl').value.trim();
            const imagePath = document.getElementById('editImagePath').value.trim();
            
            if (!question || !answer) { showToast('Compila tutti i campi', 'error'); return; }
            
            try {
                const response = await fetch('/api/edit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, category, question, answer, image_url: imageUrl, image_path: imagePath })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Salvata!', 'success');
                    closeEditModal();
                    if (!document.getElementById('leechesSection').classList.contains('hidden')) loadLeeches();
                    else if (!document.getElementById('manageCardsSection').classList.contains('hidden')) loadAllCards();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        async function deleteCard(cardId) {
            if (!confirm('Eliminare?')) return;
            try {
                const response = await fetch('/api/edit.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: cardId })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Eliminata', 'success');
                    loadAllCards();
                    loadStats();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        // === REVIEW ===
        async function startReview() {
            reviewedCount = 0;
            hideAllSections();
            document.getElementById('reviewSection').classList.remove('hidden');
            await loadNextCard();
        }
        
        async function loadNextCard() {
            try {
                const response = await fetch('/api/review.php');
                const data = await response.json();
                if (data.card) {
                    currentCard = data.card;
                    remainingCards = data.remaining_today || 0;
                    const isCloze = isClozeCard(currentCard.question);
                    document.getElementById('flashcard').classList.toggle('cloze-card', isCloze);
                    
                    if (isCloze) {
                        document.getElementById('cardQuestion').innerHTML = clozeToQuestion(currentCard.question);
                        document.getElementById('cardAnswer').innerHTML = clozeToAnswer(currentCard.question);
                    } else {
                        document.getElementById('cardQuestion').textContent = currentCard.question;
                        document.getElementById('cardAnswer').textContent = currentCard.answer;
                    }
                    
                    document.getElementById('reviewProgress').textContent = `Carta ${reviewedCount + 1} | Rimangono: ${remainingCards}`;
                    
                    // Use effective_image
                    if (currentCard.effective_image) {
                        document.getElementById('cardImage').src = currentCard.effective_image;
                        document.getElementById('cardImageContainer').classList.remove('hidden');
                    } else {
                        document.getElementById('cardImageContainer').classList.add('hidden');
                    }
                    
                    const lapses = currentCard.lapses || 0;
                    let meta = `${isCloze ? '🧩 Cloze | ' : ''}${currentCard.category || 'N/A'} | EF: ${parseFloat(currentCard.easiness_factor).toFixed(2)}`;
                    if (lapses >= 3) meta += ` | ⚠️ ${lapses}/4 fail`;
                    document.getElementById('cardMeta').innerHTML = meta;
                    
                    document.getElementById('cardAnswerSection').classList.add('hidden');
                    document.getElementById('ratingSection').classList.add('hidden');
                    document.getElementById('showAnswerBtn').classList.remove('hidden');
                } else {
                    showSessionComplete();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        function showAnswer() {
            document.getElementById('cardAnswerSection').classList.remove('hidden');
            document.getElementById('ratingSection').classList.remove('hidden');
            document.getElementById('showAnswerBtn').classList.add('hidden');
        }
        
        async function rateCard(quality) {
            if (!currentCard) return;
            try {
                const response = await fetch('/api/review.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ card_id: currentCard.id, quality: quality })
                });
                const data = await response.json();
                if (data.success) {
                    reviewedCount++;
                    sessionReviewedToday++;
                    
                    if (data.leech) {
                        showToast(data.warning, 'leech');
                        setTimeout(() => loadNextCard(), 2000);
                    } else if (data.warning) {
                        showToast(data.warning, 'warning');
                        setTimeout(() => loadNextCard(), 1200);
                    } else {
                        showToast(`Prossima: ${data.interval_days}g`, 'success');
                        setTimeout(() => loadNextCard(), 800);
                    }
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        function endReview() {
            if (reviewedCount > 0) showSessionComplete();
            else showMenu();
        }
        
        function showSessionComplete() {
            hideAllSections();
            document.getElementById('completedSection').classList.remove('hidden');
            
            let title = '🎉 Sessione Completata!';
            let summary = `Hai ripassato ${reviewedCount} carte!`;
            
            if (sessionReviewedToday >= dailyGoal) {
                title = '🏆 Obiettivo Raggiunto!';
                summary += ' Obiettivo giornaliero completato!';
                showToast('🎯 Obiettivo giornaliero completato!', 'celebration');
            }
            
            document.getElementById('completedTitle').textContent = title;
            document.getElementById('sessionSummary').textContent = summary;
        }
        
        // === SAVE CARD ===
        async function saveCard() {
            const category = document.getElementById('newCategory').value;
            const imageUrl = document.getElementById('newImageUrl').value.trim();
            const imagePath = document.getElementById('newImagePath').value.trim();
            let question, answer;
            
            if (currentCardType === 'cloze') {
                const clozeText = document.getElementById('newClozeText').value.trim();
                if (!clozeText || !isClozeCard(clozeText)) {
                    showToast('Aggiungi almeno una {{parola}}', 'error');
                    return;
                }
                question = clozeText;
                answer = extractClozeAnswers(clozeText).join(', ');
            } else {
                question = document.getElementById('newQuestion').value.trim();
                answer = document.getElementById('newAnswer').value.trim();
                if (!question || !answer) {
                    showToast('Compila tutti i campi', 'error');
                    return;
                }
            }
            
            try {
                const response = await fetch('/api/cards.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        question, 
                        answer, 
                        category, 
                        image_url: imageUrl || null,
                        image_path: imagePath || null
                    })
                });
                if (response.ok) {
                    showToast('Salvata!', 'success');
                    showMenu();
                }
            } catch (error) { showToast('Errore', 'error'); }
        }
        
        // === UTILITY ===
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast show ' + type;
            setTimeout(() => toast.classList.remove('show'), type === 'leech' || type === 'celebration' ? 5000 : 3000);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // === KEYBOARD ===
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('reviewSection').classList.contains('hidden')) return;
            if (document.getElementById('editModal').classList.contains('show')) return;
            if (e.code === 'Space') {
                e.preventDefault();
                if (!document.getElementById('showAnswerBtn').classList.contains('hidden')) showAnswer();
            } else if (e.key === '1' && !document.getElementById('ratingSection').classList.contains('hidden')) rateCard(1);
            else if (e.key === '2' && !document.getElementById('ratingSection').classList.contains('hidden')) rateCard(3);
            else if (e.key === '3' && !document.getElementById('ratingSection').classList.contains('hidden')) rateCard(5);
        });
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
</body>
</html>
