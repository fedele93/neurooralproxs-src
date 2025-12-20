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
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        /* Dashboard delle statistiche */
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
        
        .stat-box .number {
            font-size: 28px;
            font-weight: bold;
        }
        
        .stat-box .label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .stat-box.leeches {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .stat-box.leeches:hover {
            transform: scale(1.05);
        }
        
        /* Insights box */
        .insights-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .insights-box h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .insight-item {
            margin: 8px 0;
            color: #444;
        }
        
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
        
        button:hover { 
            background: #5568d3; 
            transform: translateY(-2px); 
        }
        
        button.secondary {
            background: #e0e5ff;
            color: #667eea;
        }
        
        button.secondary:hover {
            background: #c7d0ff;
        }
        
        button.danger {
            background: #ff6b6b;
        }
        
        button.danger:hover {
            background: #ee5a5a;
        }
        
        button.warning {
            background: #feca57;
            color: #333;
        }
        
        button.warning:hover {
            background: #feb940;
        }
        
        button.small {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        /* Flashcard per review */
        .flashcard {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
            min-height: 200px;
        }
        
        /* === NUOVO: Stile per carte Cloze === */
        .flashcard.cloze-card {
            border-left-color: #9b59b6;
        }
        
        .flashcard-question {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
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
        
        /* === NUOVO: Stili per Cloze === */
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
        
        .cloze-hint {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
            font-style: italic;
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
        
        .rating-btn {
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: bold;
        }
        
        .rating-btn.hard {
            background: #ff6b6b;
        }
        
        .rating-btn.hard:hover {
            background: #ee5a5a;
        }
        
        .rating-btn.good {
            background: #feca57;
            color: #333;
        }
        
        .rating-btn.good:hover {
            background: #feb940;
        }
        
        .rating-btn.easy {
            background: #26de81;
        }
        
        .rating-btn.easy:hover {
            background: #20c770;
        }
        
        /* Form per aggiungere carte */
        .form-section {
            margin: 20px 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
        }
        
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea { 
            min-height: 100px; 
            resize: vertical; 
        }
        
        /* === NUOVO: Box informativo per Cloze === */
        .cloze-help-box {
            background: #f3e8ff;
            border: 2px solid #9b59b6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .cloze-help-box h4 {
            color: #9b59b6;
            margin-bottom: 8px;
        }
        
        .cloze-help-box code {
            background: #e8d5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
        
        .cloze-help-box .example {
            background: white;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
            border-left: 3px solid #9b59b6;
        }
        
        /* Tab per tipo carta */
        .card-type-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .card-type-tab {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }
        
        .card-type-tab:hover {
            border-color: #667eea;
        }
        
        .card-type-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .card-type-tab.active.cloze {
            background: #9b59b6;
            border-color: #9b59b6;
        }
        
        /* Lista carte per gestione */
        .cards-list {
            margin-top: 20px;
        }
        
        .card-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }
        
        .card-item.leech {
            border-left-color: #ff6b6b;
            background: #fff5f5;
        }
        
        /* === NUOVO: Stile per carte cloze nella lista === */
        .card-item.cloze {
            border-left-color: #9b59b6;
        }
        
        .card-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }
        
        .card-item-content {
            flex: 1;
        }
        
        .card-item-question {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .card-item-answer {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            max-height: 60px;
            overflow: hidden;
        }
        
        .card-item-meta {
            font-size: 11px;
            color: #888;
        }
        
        .card-item-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .card-item-image {
            max-width: 80px;
            max-height: 60px;
            border-radius: 5px;
            margin-right: 10px;
        }
        
        /* Filtro categorie */
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-bar select {
            width: auto;
            min-width: 200px;
        }
        
        .filter-bar input {
            flex: 1;
            min-width: 200px;
        }
        
        /* Sezione categorie */
        .category-stats {
            margin-top: 20px;
        }
        
        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .category-name {
            font-weight: 600;
            color: #333;
        }
        
        .category-stats-mini {
            font-size: 12px;
            color: #666;
        }
        
        .category-ef {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .ef-high { background: #d4edda; color: #155724; }
        .ef-medium { background: #fff3cd; color: #856404; }
        .ef-low { background: #f8d7da; color: #721c24; }
        
        /* Box informativo leeches */
        .leech-info-box {
            background: #fff5f5;
            border: 2px solid #ff6b6b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .leech-info-box h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
        }
        
        .leech-info-box ul {
            margin-left: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .leech-info-box li {
            margin: 5px 0;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            color: #667eea;
            margin: 0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            padding: 0;
        }
        
        .modal-close:hover {
            color: #333;
            background: none;
            transform: none;
        }
        
        /* Toast notifications */
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
        
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .toast.success { background: #26de81; }
        .toast.warning { background: #feca57; color: #333; }
        .toast.error { background: #ff6b6b; }
        .toast.leech { background: #8b0000; }
        
        /* Utility */
        .hidden { display: none !important; }
        .text-center { text-align: center; }
        .mb-20 { margin-bottom: 20px; }
        
        /* Badge */
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧠 NeuroOral Pro</h1>
        <p class="subtitle">Sistema di Ripetizione Spaziata per Neurologia</p>
        
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
                <button onclick="startReview()">
                    📚 Inizia Ripasso
                </button>
                <button onclick="showAddCard()" class="secondary">
                    ➕ Aggiungi Carta
                </button>
                <button onclick="showManageCards()" class="secondary">
                    ✏️ Gestisci Carte
                </button>
                <button onclick="showCategoryStats()" class="secondary">
                    📊 Statistiche
                </button>
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
                    <img id="cardImage" class="flashcard-image" src="" alt="Immagine della carta">
                </div>
                
                <div id="cardAnswerSection" class="hidden">
                    <div class="flashcard-answer" id="cardAnswer"></div>
                </div>
                
                <div class="flashcard-meta" id="cardMeta"></div>
            </div>
            
            <div class="text-center">
                <button id="showAnswerBtn" onclick="showAnswer()">
                    👁️ Mostra Risposta
                </button>
                
                <div id="ratingSection" class="hidden">
                    <p class="mb-20" style="color: #666;">Come hai risposto?</p>
                    <div class="rating-buttons">
                        <button class="rating-btn hard" onclick="rateCard(1)">
                            ❌ Difficile (1 giorno)
                        </button>
                        <button class="rating-btn good" onclick="rateCard(3)">
                            ✓ Buona (ripeti)
                        </button>
                        <button class="rating-btn easy" onclick="rateCard(5)">
                            ⭐ Facile (allontana)
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="endReview()" class="secondary">
                    ← Torna al Menu
                </button>
            </div>
        </div>
        
        <!-- === MODIFICATO: Sezione Aggiungi Carta con supporto Cloze === -->
        <div id="addCardSection" class="hidden">
            <h2 style="margin-bottom: 20px;">➕ Aggiungi Nuova Flashcard</h2>
            
            <!-- Tab per tipo carta -->
            <div class="card-type-tabs">
                <div class="card-type-tab active" id="tabBasic" onclick="switchCardType('basic')">
                    📝 Carta Base
                </div>
                <div class="card-type-tab cloze" id="tabCloze" onclick="switchCardType('cloze')">
                    🧩 Cloze (riempi spazi)
                </div>
            </div>
            
            <!-- Help box per Cloze (nascosto di default) -->
            <div class="cloze-help-box hidden" id="clozeHelpBox">
                <h4>🧩 Come creare carte Cloze</h4>
                <p>Scrivi il testo e metti tra <code>{{doppie graffe}}</code> le parole da nascondere:</p>
                <div class="example">
                    <strong>Scrivi:</strong><br>
                    La sindrome di {{Wallenberg}} è causata da occlusione della {{PICA}}.<br><br>
                    <strong>Durante la review vedrai:</strong><br>
                    La sindrome di <span class="cloze-blank">[...]</span> è causata da occlusione della <span class="cloze-blank">[...]</span>.
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
                
                <!-- Campi per carta BASE -->
                <div id="basicCardFields">
                    <div class="form-group">
                        <label for="newQuestion">Domanda</label>
                        <textarea id="newQuestion" placeholder="Es: Quali sono le caratteristiche cliniche della sindrome di Wallenberg?"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="newAnswer">Risposta</label>
                        <textarea id="newAnswer" placeholder="Scrivi la risposta completa..." rows="6"></textarea>
                    </div>
                </div>
                
                <!-- Campi per carta CLOZE -->
                <div id="clozeCardFields" class="hidden">
                    <div class="form-group">
                        <label for="newClozeText">Testo con Cloze</label>
                        <textarea id="newClozeText" placeholder="Es: La sindrome di {{Wallenberg}} è causata da occlusione della {{PICA}} e si manifesta con {{vertigine}}, {{disfagia}} e sindrome di {{Horner}}." rows="6"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Anteprima</label>
                        <div id="clozePreview" style="background: #f8f9fa; padding: 15px; border-radius: 8px; min-height: 60px;">
                            <span style="color: #888;">L'anteprima apparirà qui...</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="newImageUrl">URL Immagine (opzionale)</label>
                    <input type="text" id="newImageUrl" placeholder="https://esempio.com/immagine.jpg">
                    <p style="font-size: 12px; color: #888; margin-top: 5px;">
                        💡 Usa immagini da Wikimedia Commons per neuroanatomia
                    </p>
                </div>
            </div>
            
            <div class="menu-buttons">
                <button onclick="saveCard()">
                    💾 Salva Carta
                </button>
                <button onclick="showMenu()" class="secondary">
                    ← Annulla
                </button>
            </div>
        </div>
        
        <!-- Sezione Gestisci Carte -->
        <div id="manageCardsSection" class="hidden">
            <h2 style="margin-bottom: 20px;">✏️ Gestisci Carte</h2>
            
            <div class="filter-bar">
                <select id="filterCategory" onchange="filterCards()">
                    <option value="">Tutte le categorie</option>
                </select>
                <input type="text" id="filterSearch" placeholder="Cerca nelle domande..." oninput="filterCards()">
            </div>
            
            <div class="cards-list" id="cardsList">
                Caricamento...
            </div>
            
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="showMenu()" class="secondary">
                    ← Torna al Menu
                </button>
            </div>
        </div>
        
        <!-- Sezione Leeches -->
        <div id="leechesSection" class="hidden">
            <h2 style="margin-bottom: 20px;">🧛 Gestione Carte Sospese (Leeches)</h2>
            
            <div class="leech-info-box">
                <h3>Cosa sono le Leeches?</h3>
                <p style="margin-bottom: 10px;">Le carte sospese hanno raggiunto 4+ fallimenti. Prima di riabilitarle:</p>
                <ul>
                    <li><strong>Dividi</strong> la carta in 2-3 carte più specifiche</li>
                    <li><strong>Aggiungi un'immagine</strong> (dual coding)</li>
                    <li><strong>Crea una mnemonica</strong> o associazione</li>
                    <li><strong>Trasforma in Cloze</strong> per mantenere il contesto</li>
                </ul>
            </div>
            
            <div class="cards-list" id="leechesList">
                Caricamento...
            </div>
            
            <div class="text-center" style="margin-top: 20px;">
                <button onclick="showMenu()" class="secondary">
                    ← Torna al Menu
                </button>
            </div>
        </div>
        
        <!-- Sezione Statistiche Dettagliate -->
        <div id="statsSection" class="hidden">
            <h2 style="margin-bottom: 20px;">📊 Statistiche per Categoria</h2>
            
            <div class="category-stats" id="categoryStatsList">
                Caricamento...
            </div>
            
            <h3 style="margin: 30px 0 15px 0;">⚠️ Carte a Rischio (3+ fallimenti)</h3>
            <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                Queste carte potrebbero diventare leeches. Considera di semplificarle.
            </p>
            <div id="problematicCardsList">
                Caricamento...
            </div>
            
            <div class="text-center" style="margin-top: 30px;">
                <button onclick="showMenu()" class="secondary">
                    ← Torna al Menu
                </button>
            </div>
        </div>
        
        <!-- Sessione completata -->
        <div id="completedSection" class="hidden">
            <div class="text-center" style="padding: 40px 0;">
                <h2 style="color: #26de81; margin-bottom: 20px;">🎉 Sessione Completata!</h2>
                <p style="color: #666; margin-bottom: 30px;" id="sessionSummary"></p>
                <button onclick="showMenu()">
                    ← Torna al Menu
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal Modifica Carta -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Modifica Carta</h2>
                <button class="modal-close" onclick="closeEditModal()">×</button>
            </div>
            
            <input type="hidden" id="editCardId">
            
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
                <label for="editQuestion">Domanda / Testo Cloze</label>
                <textarea id="editQuestion" rows="3"></textarea>
                <p style="font-size: 12px; color: #888; margin-top: 5px;">
                    💡 Per Cloze: usa {{doppie graffe}} per nascondere parole
                </p>
            </div>
            
            <div class="form-group">
                <label for="editAnswer">Risposta</label>
                <textarea id="editAnswer" rows="6"></textarea>
            </div>
            
            <div class="form-group">
                <label for="editImageUrl">URL Immagine (opzionale)</label>
                <input type="text" id="editImageUrl" placeholder="https://esempio.com/immagine.jpg">
            </div>
            
            <div id="editImagePreview" class="hidden" style="margin-bottom: 15px;">
                <img id="editImagePreviewImg" style="max-width: 200px; border-radius: 8px;">
            </div>
            
            <div class="menu-buttons">
                <button onclick="saveEditCard()">
                    💾 Salva Modifiche
                </button>
                <button onclick="closeEditModal()" class="secondary">
                    Annulla
                </button>
            </div>
        </div>
    </div>
    
    <!-- Toast notification -->
    <div class="toast" id="toast"></div>

    <script>
        // Variabili globali
        let currentCard = null;
        let reviewedCount = 0;
        let remainingCards = 0;
        let allCards = [];
        let currentCardType = 'basic'; // 'basic' o 'cloze'
        
        /**
         * INIZIALIZZAZIONE
         */
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            
            // Listener per anteprima cloze in tempo reale
            document.getElementById('newClozeText').addEventListener('input', updateClozePreview);
        });
        
        /**
         * === NUOVO: Funzioni per Cloze ===
         */
        
        // Verifica se un testo è una carta Cloze
        function isClozeCard(text) {
            return /\{\{.+?\}\}/.test(text);
        }
        
        // Conta quante cloze ci sono
        function countClozes(text) {
            const matches = text.match(/\{\{.+?\}\}/g);
            return matches ? matches.length : 0;
        }
        
        // Converte testo cloze in versione con blanks (per la domanda)
        function clozeToQuestion(text) {
            return text.replace(/\{\{(.+?)\}\}/g, '<span class="cloze-blank">[...]</span>');
        }
        
        // Converte testo cloze in versione con risposte evidenziate
        function clozeToAnswer(text) {
            return text.replace(/\{\{(.+?)\}\}/g, '<span class="cloze-revealed">$1</span>');
        }
        
        // Estrae le risposte da un testo cloze
        function extractClozeAnswers(text) {
            const matches = text.match(/\{\{(.+?)\}\}/g);
            if (!matches) return [];
            return matches.map(m => m.replace(/\{\{|\}\}/g, ''));
        }
        
        // Aggiorna anteprima cloze
        function updateClozePreview() {
            const text = document.getElementById('newClozeText').value;
            const preview = document.getElementById('clozePreview');
            
            if (text.trim() === '') {
                preview.innerHTML = '<span style="color: #888;">L\'anteprima apparirà qui...</span>';
                return;
            }
            
            const numClozes = countClozes(text);
            if (numClozes === 0) {
                preview.innerHTML = '<span style="color: #ff6b6b;">⚠️ Aggiungi almeno una {{parola}} tra doppie graffe</span>';
                return;
            }
            
            const questionView = clozeToQuestion(text);
            preview.innerHTML = `
                <div style="margin-bottom: 10px;"><strong>Domanda:</strong></div>
                <div style="margin-bottom: 15px;">${questionView}</div>
                <div style="color: #26de81; font-size: 12px;">✓ ${numClozes} campo/i da ricordare</div>
            `;
        }
        
        // Cambia tipo carta (basic/cloze)
        function switchCardType(type) {
            currentCardType = type;
            
            // Aggiorna tabs
            document.getElementById('tabBasic').classList.toggle('active', type === 'basic');
            document.getElementById('tabCloze').classList.toggle('active', type === 'cloze');
            
            // Mostra/nascondi campi appropriati
            document.getElementById('basicCardFields').classList.toggle('hidden', type === 'cloze');
            document.getElementById('clozeCardFields').classList.toggle('hidden', type === 'basic');
            document.getElementById('clozeHelpBox').classList.toggle('hidden', type === 'basic');
        }
        
        /**
         * NAVIGAZIONE
         */
        function showMenu() {
            hideAllSections();
            document.getElementById('menuSection').classList.remove('hidden');
            document.getElementById('statsDashboard').classList.remove('hidden');
            document.getElementById('insightsBox').classList.remove('hidden');
            loadStats();
        }
        
        function hideAllSections() {
            ['menuSection', 'reviewSection', 'addCardSection', 'manageCardsSection', 
             'statsSection', 'completedSection', 'leechesSection'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
        }
        
        function showAddCard() {
            hideAllSections();
            document.getElementById('addCardSection').classList.remove('hidden');
            
            // Reset form
            document.getElementById('newQuestion').value = '';
            document.getElementById('newAnswer').value = '';
            document.getElementById('newClozeText').value = '';
            document.getElementById('newImageUrl').value = '';
            document.getElementById('clozePreview').innerHTML = '<span style="color: #888;">L\'anteprima apparirà qui...</span>';
            
            // Default a carta base
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
        
        /**
         * STATISTICHE
         */
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
                
                const leechBox = document.getElementById('statLeechesBox');
                if (leechCount === 0) {
                    leechBox.style.display = 'none';
                } else {
                    leechBox.style.display = 'block';
                }
                
                if (data.insights && data.insights.length > 0) {
                    document.getElementById('insightsList').innerHTML = 
                        data.insights.map(i => `<div class="insight-item">${i}</div>`).join('');
                } else {
                    document.getElementById('insightsList').innerHTML = 
                        '<div class="insight-item">✅ Tutto bene! Continua così.</div>';
                }
                
            } catch (error) {
                console.error('Errore caricamento stats:', error);
            }
        }
        
        async function loadLeeches() {
            try {
                const response = await fetch('/api/leeches.php');
                const data = await response.json();
                
                if (data.leeches && data.leeches.length > 0) {
                    document.getElementById('leechesList').innerHTML = data.leeches.map(card => {
                        const isCloze = isClozeCard(card.question);
                        return `
                            <div class="card-item leech ${isCloze ? 'cloze' : ''}">
                                <div class="card-item-header">
                                    <div class="card-item-content">
                                        <div class="card-item-question">
                                            ${escapeHtml(card.question)}
                                            <span class="leech-badge">${card.lapses} fallimenti</span>
                                            ${isCloze ? '<span class="cloze-type-badge">Cloze</span>' : ''}
                                        </div>
                                        <div class="card-item-answer">${escapeHtml(card.answer)}</div>
                                        <div class="card-item-meta">
                                            ${card.category || 'Senza categoria'} | EF: ${parseFloat(card.easiness_factor).toFixed(2)}
                                        </div>
                                    </div>
                                    <div class="card-item-actions">
                                        <button class="small secondary" onclick="openEditModal(${card.id})">✏️ Modifica</button>
                                        <button class="small warning" onclick="reactivateLeech(${card.id})">🔄 Riattiva</button>
                                        <button class="small danger" onclick="deleteLeech(${card.id})">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    document.getElementById('leechesList').innerHTML = 
                        '<p style="text-align: center; color: #26de81; padding: 30px;">✅ Nessuna carta sospesa! Ottimo lavoro!</p>';
                }
                
            } catch (error) {
                console.error('Errore:', error);
                document.getElementById('leechesList').innerHTML = '<p>Errore nel caricamento.</p>';
            }
        }
        
        async function reactivateLeech(cardId) {
            if (!confirm('Hai modificato/semplificato questa carta? Riattivandola apparirà domani nel ripasso.')) {
                return;
            }
            
            try {
                const response = await fetch('/api/leeches.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reactivate', card_id: cardId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Carta riattivata! Apparirà domani.', 'success');
                    loadLeeches();
                    loadStats();
                } else {
                    showToast(data.error || 'Errore', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
            }
        }
        
        async function deleteLeech(cardId) {
            if (!confirm('Eliminare definitivamente questa carta? L\'azione non può essere annullata.')) {
                return;
            }
            
            try {
                const response = await fetch('/api/leeches.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', card_id: cardId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Carta eliminata', 'success');
                    loadLeeches();
                    loadStats();
                } else {
                    showToast(data.error || 'Errore', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
            }
        }
        
        async function loadDetailedStats() {
            try {
                const response = await fetch('/api/stats.php');
                const data = await response.json();
                
                if (data.by_category && data.by_category.length > 0) {
                    document.getElementById('categoryStatsList').innerHTML = data.by_category.map(cat => {
                        const efClass = cat.avg_ef >= 2.3 ? 'ef-high' : (cat.avg_ef >= 2.0 ? 'ef-medium' : 'ef-low');
                        const suspendedInfo = cat.suspended > 0 ? ` | 🧛 ${cat.suspended} sospese` : '';
                        return `
                            <div class="category-item">
                                <div>
                                    <div class="category-name">${cat.category}</div>
                                    <div class="category-stats-mini">
                                        ${cat.total} carte | ${cat.due} da ripassare | ${cat.total_lapses || 0} fallimenti${suspendedInfo}
                                    </div>
                                </div>
                                <span class="category-ef ${efClass}">EF: ${cat.avg_ef || 'N/A'}</span>
                            </div>
                        `;
                    }).join('');
                } else {
                    document.getElementById('categoryStatsList').innerHTML = '<p>Nessuna categoria trovata.</p>';
                }
                
                if (data.problematic_cards && data.problematic_cards.length > 0) {
                    document.getElementById('problematicCardsList').innerHTML = data.problematic_cards
                        .filter(card => !card.suspended)
                        .map(card => `
                        <div class="category-item">
                            <div>
                                <div class="category-name">${card.question.substring(0, 60)}...</div>
                                <div class="category-stats-mini">
                                    ${card.category || 'Senza categoria'} | ${card.lapses} fallimenti (diventa leech a 4)
                                </div>
                            </div>
                            <span class="category-ef ef-low">EF: ${parseFloat(card.easiness_factor).toFixed(2)}</span>
                        </div>
                    `).join('') || '<p style="color: #26de81;">✅ Nessuna carta a rischio!</p>';
                } else {
                    document.getElementById('problematicCardsList').innerHTML = 
                        '<p style="color: #26de81;">✅ Nessuna carta a rischio!</p>';
                }
                
            } catch (error) {
                console.error('Errore:', error);
            }
        }
        
        /**
         * GESTIONE CARTE - Lista e Filtri
         */
        async function loadAllCards() {
            try {
                const response = await fetch('/api/cards.php');
                const data = await response.json();
                allCards = data.cards || [];
                
                const categories = [...new Set(allCards.map(c => c.category).filter(Boolean))];
                const filterSelect = document.getElementById('filterCategory');
                filterSelect.innerHTML = '<option value="">Tutte le categorie</option>' +
                    categories.map(c => `<option value="${c}">${c}</option>`).join('');
                
                renderCardsList(allCards);
                
            } catch (error) {
                console.error('Errore:', error);
                document.getElementById('cardsList').innerHTML = '<p>Errore nel caricamento delle carte.</p>';
            }
        }
        
        function filterCards() {
            const category = document.getElementById('filterCategory').value;
            const search = document.getElementById('filterSearch').value.toLowerCase();
            
            let filtered = allCards;
            
            if (category) {
                filtered = filtered.filter(c => c.category === category);
            }
            
            if (search) {
                filtered = filtered.filter(c => 
                    c.question.toLowerCase().includes(search) || 
                    c.answer.toLowerCase().includes(search)
                );
            }
            
            renderCardsList(filtered);
        }
        
        function renderCardsList(cards) {
            if (cards.length === 0) {
                document.getElementById('cardsList').innerHTML = '<p>Nessuna carta trovata.</p>';
                return;
            }
            
            document.getElementById('cardsList').innerHTML = cards.map(card => {
                const ef = parseFloat(card.easiness_factor).toFixed(2);
                const lapses = card.lapses || 0;
                const isSuspended = card.suspended == 1;
                const isCloze = isClozeCard(card.question);
                const imageHtml = card.image_url ? 
                    `<img src="${card.image_url}" class="card-item-image" onerror="this.style.display='none'">` : '';
                const leechBadge = isSuspended ? '<span class="leech-badge">🧛 Sospesa</span>' : '';
                const clozeBadge = isCloze ? '<span class="cloze-type-badge">Cloze</span>' : '';
                
                let cardClass = 'card-item';
                if (isSuspended) cardClass += ' leech';
                else if (isCloze) cardClass += ' cloze';
                
                return `
                    <div class="${cardClass}">
                        <div class="card-item-header">
                            ${imageHtml}
                            <div class="card-item-content">
                                <div class="card-item-question">${escapeHtml(card.question)} ${leechBadge} ${clozeBadge}</div>
                                <div class="card-item-answer">${escapeHtml(card.answer)}</div>
                                <div class="card-item-meta">
                                    ${card.category || 'Senza categoria'} | EF: ${ef} | Fallimenti: ${lapses}
                                </div>
                            </div>
                            <div class="card-item-actions">
                                <button class="small secondary" onclick="openEditModal(${card.id})">✏️ Modifica</button>
                                ${isSuspended ? 
                                    `<button class="small warning" onclick="reactivateLeech(${card.id})">🔄 Riattiva</button>` : 
                                    ''}
                                <button class="small danger" onclick="deleteCard(${card.id})">🗑️</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        /**
         * MODIFICA CARTA - Modal
         */
        async function openEditModal(cardId) {
            try {
                const response = await fetch(`/api/edit.php?id=${cardId}`);
                const data = await response.json();
                
                if (data.success && data.card) {
                    const card = data.card;
                    document.getElementById('editCardId').value = card.id;
                    document.getElementById('editCategory').value = card.category || 'Altro';
                    document.getElementById('editQuestion').value = card.question;
                    document.getElementById('editAnswer').value = card.answer;
                    document.getElementById('editImageUrl').value = card.image_url || '';
                    
                    if (card.image_url) {
                        document.getElementById('editImagePreviewImg').src = card.image_url;
                        document.getElementById('editImagePreview').classList.remove('hidden');
                    } else {
                        document.getElementById('editImagePreview').classList.add('hidden');
                    }
                    
                    document.getElementById('editModal').classList.add('show');
                } else {
                    showToast('Carta non trovata', 'error');
                }
                
            } catch (error) {
                showToast('Errore nel caricamento', 'error');
                console.error(error);
            }
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }
        
        async function saveEditCard() {
            const id = document.getElementById('editCardId').value;
            const category = document.getElementById('editCategory').value;
            const question = document.getElementById('editQuestion').value.trim();
            const answer = document.getElementById('editAnswer').value.trim();
            const imageUrl = document.getElementById('editImageUrl').value.trim();
            
            if (!question || !answer) {
                showToast('Domanda e risposta sono obbligatorie', 'error');
                return;
            }
            
            try {
                const response = await fetch('/api/edit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, category, question, answer, image_url: imageUrl })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Carta aggiornata! ✅', 'success');
                    closeEditModal();
                    
                    if (!document.getElementById('leechesSection').classList.contains('hidden')) {
                        loadLeeches();
                    } else if (!document.getElementById('manageCardsSection').classList.contains('hidden')) {
                        loadAllCards();
                    }
                } else {
                    showToast(data.error || 'Errore nel salvataggio', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
                console.error(error);
            }
        }
        
        /**
         * ELIMINA CARTA
         */
        async function deleteCard(cardId) {
            if (!confirm('Sei sicuro di voler eliminare questa carta?')) {
                return;
            }
            
            try {
                const response = await fetch('/api/edit.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: cardId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Carta eliminata', 'success');
                    loadAllCards();
                    loadStats();
                } else {
                    showToast(data.error || 'Errore', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
                console.error(error);
            }
        }
        
        /**
         * === MODIFICATO: SESSIONE DI REVIEW con supporto Cloze ===
         */
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
                    
                    const flashcardEl = document.getElementById('flashcard');
                    const isCloze = isClozeCard(currentCard.question);
                    
                    // Aggiorna stile flashcard
                    flashcardEl.classList.toggle('cloze-card', isCloze);
                    
                    // Mostra domanda (con cloze blanks se necessario)
                    if (isCloze) {
                        document.getElementById('cardQuestion').innerHTML = clozeToQuestion(currentCard.question);
                    } else {
                        document.getElementById('cardQuestion').textContent = currentCard.question;
                    }
                    
                    // Prepara risposta (con cloze evidenziati se necessario)
                    if (isCloze) {
                        document.getElementById('cardAnswer').innerHTML = clozeToAnswer(currentCard.question);
                    } else {
                        document.getElementById('cardAnswer').textContent = currentCard.answer;
                    }
                    
                    document.getElementById('reviewProgress').textContent = 
                        `Carta ${reviewedCount + 1} | Rimangono: ${remainingCards}`;
                    
                    // Immagine
                    if (currentCard.image_url) {
                        document.getElementById('cardImage').src = currentCard.image_url;
                        document.getElementById('cardImageContainer').classList.remove('hidden');
                    } else {
                        document.getElementById('cardImageContainer').classList.add('hidden');
                    }
                    
                    // Meta info
                    const lapses = currentCard.lapses || 0;
                    const ef = parseFloat(currentCard.easiness_factor).toFixed(2);
                    let metaHtml = `Categoria: ${currentCard.category || 'Generale'} | EF: ${ef}`;
                    if (isCloze) {
                        metaHtml = `🧩 Cloze | ` + metaHtml;
                    }
                    if (lapses >= 3) {
                        metaHtml += ` | ⚠️ ${lapses}/4 fallimenti`;
                    }
                    document.getElementById('cardMeta').innerHTML = metaHtml;
                    
                    // Reset UI
                    document.getElementById('cardAnswerSection').classList.add('hidden');
                    document.getElementById('ratingSection').classList.add('hidden');
                    document.getElementById('showAnswerBtn').classList.remove('hidden');
                    
                } else {
                    showSessionComplete();
                }
                
            } catch (error) {
                showToast('Errore nel caricamento della carta', 'error');
                console.error(error);
            }
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
                    
                    if (data.leech) {
                        showToast(data.warning, 'leech');
                        setTimeout(() => loadNextCard(), 2000);
                    } else if (data.warning) {
                        showToast(data.warning, 'warning');
                        setTimeout(() => loadNextCard(), 1200);
                    } else {
                        let message = `Prossima review: ${data.interval_days} giorn${data.interval_days === 1 ? 'o' : 'i'}`;
                        showToast(message, 'success');
                        setTimeout(() => loadNextCard(), 800);
                    }
                    
                } else {
                    showToast('Errore nel salvataggio', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
                console.error(error);
            }
        }
        
        function endReview() {
            if (reviewedCount > 0) {
                showSessionComplete();
            } else {
                showMenu();
            }
        }
        
        function showSessionComplete() {
            hideAllSections();
            document.getElementById('completedSection').classList.remove('hidden');
            document.getElementById('sessionSummary').textContent = 
                `Hai ripassato ${reviewedCount} carte! 🎓`;
        }
        
        /**
         * === MODIFICATO: AGGIUNTA CARTE con supporto Cloze ===
         */
        async function saveCard() {
            const category = document.getElementById('newCategory').value;
            const imageUrl = document.getElementById('newImageUrl').value.trim();
            
            let question, answer;
            
            if (currentCardType === 'cloze') {
                // Carta Cloze
                const clozeText = document.getElementById('newClozeText').value.trim();
                
                if (!clozeText) {
                    showToast('Inserisci il testo cloze!', 'error');
                    return;
                }
                
                if (!isClozeCard(clozeText)) {
                    showToast('Aggiungi almeno una {{parola}} tra doppie graffe!', 'error');
                    return;
                }
                
                question = clozeText;
                // La risposta per le cloze è l'elenco delle parole nascoste
                const answers = extractClozeAnswers(clozeText);
                answer = answers.join(', ');
                
            } else {
                // Carta Base
                question = document.getElementById('newQuestion').value.trim();
                answer = document.getElementById('newAnswer').value.trim();
                
                if (!question || !answer) {
                    showToast('Compila domanda e risposta!', 'error');
                    return;
                }
            }
            
            try {
                const response = await fetch('/api/cards.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ question, answer, category, image_url: imageUrl || null })
                });
                
                if (response.ok) {
                    const cardTypeLabel = currentCardType === 'cloze' ? 'Cloze' : 'Base';
                    showToast(`Carta ${cardTypeLabel} salvata! ✅`, 'success');
                    showMenu();
                } else {
                    showToast('Errore nel salvataggio', 'error');
                }
                
            } catch (error) {
                showToast('Errore di connessione', 'error');
                console.error(error);
            }
        }
        
        /**
         * UTILITY
         */
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast show ' + type;
            
            const duration = type === 'leech' ? 5000 : 3000;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        /**
         * KEYBOARD SHORTCUTS
         */
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('reviewSection').classList.contains('hidden')) return;
            if (document.getElementById('editModal').classList.contains('show')) return;
            
            if (e.code === 'Space') {
                e.preventDefault();
                if (!document.getElementById('showAnswerBtn').classList.contains('hidden')) {
                    showAnswer();
                }
            } else if (e.key === '1' && !document.getElementById('ratingSection').classList.contains('hidden')) {
                rateCard(1);
            } else if (e.key === '2' && !document.getElementById('ratingSection').classList.contains('hidden')) {
                rateCard(3);
            } else if (e.key === '3' && !document.getElementById('ratingSection').classList.contains('hidden')) {
                rateCard(5);
            }
        });
        
        // Chiudi modal cliccando fuori
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
