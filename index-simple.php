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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
        }
        h1 { color: #667eea; margin-bottom: 30px; text-align: center; }
        .card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        button:hover { background: #5568d3; transform: translateY(-2px); }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        textarea { min-height: 100px; resize: vertical; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧠 NeuroOral Pro SRS</h1>
        
        <div id="menu">
            <button onclick="showAddCard()">➕ Aggiungi Flashcard</button>
            <button onclick="showReview()">📚 Ripassa</button>
            <button onclick="showStats()">📊 Statistiche</button>
        </div>

        <div id="addCard" class="hidden">
            <h2>Aggiungi Nuova Flashcard</h2>
            <input type="text" id="question" placeholder="Domanda...">
            <textarea id="answer" placeholder="Risposta..."></textarea>
            <button onclick="saveCard()">Salva</button>
            <button onclick="showMenu()">Annulla</button>
        </div>

        <div id="review" class="hidden">
            <h2>Ripasso</h2>
            <div id="reviewCard" class="card">
                <h3 id="reviewQuestion"></h3>
                <div id="reviewAnswer" class="hidden"></div>
            </div>
            <button onclick="showAnswer()">Mostra Risposta</button>
            <div id="reviewButtons" class="hidden">
                <button onclick="rateCard(1)">❌ Difficile</button>
                <button onclick="rateCard(3)">✓ Buona</button>
                <button onclick="rateCard(5)">✓✓ Facile</button>
            </div>
            <button onclick="showMenu()">Torna al Menu</button>
        </div>

        <div id="stats" class="hidden">
            <h2>Statistiche</h2>
            <div id="statsContent" class="card"></div>
            <button onclick="showMenu()">Torna al Menu</button>
        </div>
    </div>

    <script>
        let currentCard = null;

        function showMenu() {
            document.querySelectorAll('.container > div').forEach(d => d.classList.add('hidden'));
            document.getElementById('menu').classList.remove('hidden');
        }

        function showAddCard() {
            document.querySelectorAll('.container > div').forEach(d => d.classList.add('hidden'));
            document.getElementById('addCard').classList.remove('hidden');
            document.getElementById('question').value = '';
            document.getElementById('answer').value = '';
        }

        function showReview() {
            document.querySelectorAll('.container > div').forEach(d => d.classList.add('hidden'));
            document.getElementById('review').classList.remove('hidden');
            loadReviewCard();
        }

        function showStats() {
            document.querySelectorAll('.container > div').forEach(d => d.classList.add('hidden'));
            document.getElementById('stats').classList.remove('hidden');
            loadStats();
        }

        async function saveCard() {
            const question = document.getElementById('question').value;
            const answer = document.getElementById('answer').value;
            
            if (!question || !answer) {
                alert('Compila entrambi i campi!');
                return;
            }

            try {
                const response = await fetch('/api/cards.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ question, answer })
                });
                
                if (response.ok) {
                    alert('Flashcard salvata!');
                    showMenu();
                } else {
                    alert('Errore nel salvataggio');
                }
            } catch (error) {
                alert('Errore di connessione: ' + error.message);
            }
        }

        async function loadReviewCard() {
            try {
                const response = await fetch('/api/review.php');
                const data = await response.json();
                
                if (data.card) {
                    currentCard = data.card;
                    document.getElementById('reviewQuestion').textContent = data.card.question;
                    document.getElementById('reviewAnswer').textContent = data.card.answer;
                    document.getElementById('reviewAnswer').classList.add('hidden');
                    document.getElementById('reviewButtons').classList.add('hidden');
                } else {
                    alert('Nessuna card da ripassare!');
                    showMenu();
                }
            } catch (error) {
                alert('Errore: ' + error.message);
            }
        }

        function showAnswer() {
            document.getElementById('reviewAnswer').classList.remove('hidden');
            document.getElementById('reviewButtons').classList.remove('hidden');
        }

        async function rateCard(quality) {
            if (!currentCard) return;

            try {
                const response = await fetch('/api/review.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ card_id: currentCard.id, quality })
                });

                if (response.ok) {
                    loadReviewCard();
                }
            } catch (error) {
                alert('Errore: ' + error.message);
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('/api/stats.php');
                const data = await response.json();
                
                document.getElementById('statsContent').innerHTML = `
                    <p><strong>Totale Flashcard:</strong> ${data.total || 0}</p>
                    <p><strong>Da Ripassare:</strong> ${data.due || 0}</p>
                    <p><strong>Nuove:</strong> ${data.new || 0}</p>
                `;
            } catch (error) {
                alert('Errore: ' + error.message);
            }
        }
    </script>
</body>
</html>
