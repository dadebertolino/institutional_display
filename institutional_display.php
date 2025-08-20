<?php

// Includi i file esterni
require_once 'get_current_period.php';
require_once 'forecast.php';

// Configurazione dell'applicazione
$config = [
    'institution_name' => 'ISTITUTO DI ISTRUZIONE SUPERIORE',
    'institution_subtitle' => 'CIGNA-BARUFFI-GARELLI',
    'logo_url' => 'https://testdelsito.eu/Cigna2/wp-content/uploads/2025/08/logo-cigna-1.png',
    'logo_alt' => 'Logo Istituto',
    'page_title' => 'Display Istituzionale',
    'lang' => 'it'
];

// Array dei messaggi
$messages = [
    "Benvenuti nel nostro istituto",
    "Segreteria: Lu-Ve 7:30-8:00 e 10:15-12:00",
    "Seguire le indicazioni di sicurezza",
    "Consultare la bacheca per gli avvisi",
    "Mantenere il silenzio nelle aule",
    "I dispositivi mobili devono essere silenziati",
    "Rispettare gli orari di ingresso e uscita"
];

// Funzione per generare i messaggi in JSON per JavaScript
function getMessagesJson($messages) {
    return json_encode($messages, JSON_UNESCAPED_UNICODE);
}

// Funzione per ottenere la data e ora iniziale (server-side)
function getInitialDateTime() {
    date_default_timezone_set('Europe/Rome');
    return [
        'time' => date('H:i:s'),
        'date' => strtoupper(strftime('%A, %d %B %Y'))
    ];
}

$initialDateTime = getInitialDateTime();
$currentPeriod = getCurrentPeriod();
?>
<!DOCTYPE html>
<html lang="<?php echo $config['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title><?php echo htmlspecialchars($config['page_title']); ?></title>
    <link rel="stylesheet" href="institutional.css">
</head>
<body>
    <!-- Settore superiore: Logo e nome istituzione -->
    <section class="header-section">
        <div class="logo-container">
            <div class="logo" id="logoContainer">
                <img id="logoImage" src="<?php echo htmlspecialchars($config['logo_url']); ?>" alt="<?php echo htmlspecialchars($config['logo_alt']); ?>">
            </div>
            <h1 class="institution-name">
                <?php echo htmlspecialchars($config['institution_name']); ?><br>
                <?php echo htmlspecialchars($config['institution_subtitle']); ?>
            </h1>
        </div>
    </section>

    <!-- Settore centrale: Ora e data -->
    <section class="center-section">
        <div class="time-display" id="time"><?php echo $initialDateTime['time']; ?></div>
        <div class="date-display" id="date"><?php echo $initialDateTime['date']; ?></div>
        <div class="hour-display" id="hourDisplay"><?php echo htmlspecialchars($currentPeriod); ?></div>
    </section>

    <!-- Settore inferiore: Meteo e Messaggi -->
    <section class="footer-section">
        <!-- Widget Meteo -->
        <?php echo generateWeatherWidget(); ?>

        <!-- Container Messaggi -->
        <div class="messages-container" id="messagesContainer">
            <?php 
            // Visualizza i primi 3 messaggi inizialmente
            for ($i = 0; $i < min(3, count($messages)); $i++) {
                $activeClass = ($i === 1) ? ' active' : '';
                echo '<div class="message' . $activeClass . '">' . htmlspecialchars($messages[$i]) . '</div>';
            }
            ?>
        </div>
    </section>

        <script>
        // Configurazione JavaScript generata da PHP
        const messages = <?php echo getMessagesJson($messages); ?>;
        let currentMessageIndex = 0;

        // Funzione per aggiornare il periodo corrente
        function updateCurrentPeriod() {
            // Chiamata AJAX per aggiornare il periodo
            fetch('getCurrentPeriod.php')
                .then(response => response.text())
                .then(period => {
                    document.getElementById('hourDisplay').textContent = period;
                })
                .catch(error => {
                    console.error('Errore nell\'aggiornamento del periodo:', error);
                });
        }

        // Funzione per aggiornare il meteo tramite AJAX
        function updateWeather() {
            fetch('weatherWidget.php?ajax=weather')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('weatherIcon').textContent = data.icon;
                    document.getElementById('weatherTemp').textContent = data.temp;
                    document.getElementById('weatherDescription').textContent = data.description;
                    document.getElementById('weatherHumidity').textContent = data.humidity;
                    document.getElementById('weatherWind').textContent = data.wind;
                    document.getElementById('weatherPressure').textContent = data.pressure;
                    document.getElementById('weatherVisibility').textContent = data.visibility;
                })
                .catch(error => {
                    console.error('Errore nell\'aggiornamento del meteo:', error);
                });
        }

        // Funzione per aggiornare l'ora
        function updateTime() {
            const now = new Date();
            
            // Formattazione ora
            const timeString = now.toLocaleTimeString('it-IT', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            // Formattazione data
            const dateString = now.toLocaleDateString('it-IT', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).toUpperCase();
            
            document.getElementById('time').textContent = timeString;
            document.getElementById('date').textContent = dateString;
        }

        // Funzione per aggiornare i messaggi
        function updateMessages() {
            const container = document.getElementById('messagesContainer');
            
            // Mostra 3 messaggi alla volta
            const messagesToShow = [];
            for (let i = 0; i < 3; i++) {
                messagesToShow.push(messages[(currentMessageIndex + i) % messages.length]);
            }
            
            container.innerHTML = messagesToShow
                .map((msg, index) => `<div class="message ${index === 1 ? 'active' : ''}">${msg}</div>`)
                .join('');
            
            currentMessageIndex = (currentMessageIndex + 1) % messages.length;
        }

        // Inizializzazione
        updateTime();
        updateWeather();
        
        // Aggiornamento ogni secondo per l'ora
        setInterval(updateTime, 1000);
        
        // Rotazione messaggi ogni 5 secondi
        setInterval(updateMessages, 5000);

        // Aggiornamento meteo ogni 30 minuti (1800000 ms)
        setInterval(updateWeather, 1800000);

        // Aggiornamento periodo corrente ogni minuto (60000 ms)
        setInterval(updateCurrentPeriod, 60000);

        // Log informativo per sviluppatori
        console.log('Display Istituzionale - Versione PHP');
        console.log('Messaggi caricati:', messages.length);
    </script>
    </body>
</html>