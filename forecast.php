<?php
// File: forecast.php
// Gestisce la generazione del widget meteo con API OpenWeatherMap

// Configurazione meteo
$weatherConfig = [
    'api_key' => 'API_KEY', // Sostituire con la tua API key di OpenWeatherMap
    'city' => 'Mondovì',
    'country_code' => 'IT',
    'units' => 'metric', // metric, imperial, kelvin
    'language' => 'it',
    'cache_duration' => 600, // 10 minuti in secondi
    'cache_file' => 'weather_cache.json',
    'default_data' => [
        'icon' => '☀️',
        'temp' => '22°C',
        'description' => 'Soleggiato',
        'humidity' => '65%',
        'wind' => '12 km/h',
        'pressure' => '1013 hPa',
        'visibility' => '10 km'
    ]
];

// Funzione per convertire codice icona OpenWeather in emoji
function getWeatherEmoji($iconCode) {
    $iconMap = [
        '01d' => '☀️',   // clear sky day
        '01n' => '🌙',   // clear sky night
        '02d' => '⛅',   // few clouds day
        '02n' => '☁️',   // few clouds night
        '03d' => '☁️',   // scattered clouds
        '03n' => '☁️',   // scattered clouds
        '04d' => '☁️',   // broken clouds
        '04n' => '☁️',   // broken clouds
        '09d' => '🌧️',   // shower rain
        '09n' => '🌧️',   // shower rain
        '10d' => '🌦️',   // rain day
        '10n' => '🌧️',   // rain night
        '11d' => '⛈️',   // thunderstorm
        '11n' => '⛈️',   // thunderstorm
        '13d' => '❄️',   // snow
        '13n' => '❄️',   // snow
        '50d' => '🌫️',   // mist
        '50n' => '🌫️',   // mist
    ];
    
    return isset($iconMap[$iconCode]) ? $iconMap[$iconCode] : '☀️';
}

// Funzione per verificare se la cache è valida
function isCacheValid() {
    global $weatherConfig;
    
    if (!file_exists($weatherConfig['cache_file'])) {
        return false;
    }
    
    $cacheTime = filemtime($weatherConfig['cache_file']);
    return (time() - $cacheTime) < $weatherConfig['cache_duration'];
}

// Funzione per leggere dalla cache
function readCache() {
    global $weatherConfig;
    
    if (file_exists($weatherConfig['cache_file'])) {
        $cacheContent = file_get_contents($weatherConfig['cache_file']);
        return json_decode($cacheContent, true);
    }
    
    return null;
}

// Funzione per scrivere nella cache
function writeCache($data) {
    global $weatherConfig;
    file_put_contents($weatherConfig['cache_file'], json_encode($data));
}

// Funzione per chiamare l'API OpenWeatherMap
function fetchWeatherFromAPI() {
    global $weatherConfig;
    
    // Verifica che sia stata configurata una API key valida
    if ($weatherConfig['api_key'] === 'YOUR_API_KEY_HERE' || empty($weatherConfig['api_key'])) {
        return null; // Usa i dati di fallback
    }
    
    $apiUrl = sprintf(
        'https://api.openweathermap.org/data/2.5/weather?q=%s,%s&appid=%s&units=%s&lang=%s',
        urlencode($weatherConfig['city']),
        $weatherConfig['country_code'],
        $weatherConfig['api_key'],
        $weatherConfig['units'],
        $weatherConfig['language']
    );
    
    // Usa cURL per la chiamata API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'School Display Widget 1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response !== false) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['main'])) {
            // Trasforma i dati API nel formato del nostro widget
            $weatherData = [
                'icon' => getWeatherEmoji($data['weather'][0]['icon']),
                'temp' => round($data['main']['temp']) . '°C',
                'description' => ucfirst($data['weather'][0]['description']),
                'humidity' => $data['main']['humidity'] . '%',
                'wind' => round($data['wind']['speed'] * 3.6) . ' km/h', // m/s to km/h
                'pressure' => $data['main']['pressure'] . ' hPa',
                'visibility' => isset($data['visibility']) ? round($data['visibility'] / 1000, 1) . ' km' : 'N/A'
            ];
            
            // Salva nella cache
            writeCache($weatherData);
            return $weatherData;
        }
    }
    
    return null; // Errore nella chiamata API
}

// Funzione principale per ottenere i dati meteo
function getWeatherData() {
    global $weatherConfig;
    
    // Controlla prima la cache
    if (isCacheValid()) {
        $cachedData = readCache();
        if ($cachedData) {
            return $cachedData;
        }
    }
    
    // Prova a ottenere dati freschi dall'API
    $apiData = fetchWeatherFromAPI();
    if ($apiData) {
        return $apiData;
    }
    
    // Usa dati dalla cache anche se scaduti
    $cachedData = readCache();
    if ($cachedData) {
        return $cachedData;
    }
    
    // Ultimo fallback: dati di default
    return $weatherConfig['default_data'];
}

// Funzione per generare il widget HTML
function generateWeatherWidget() {
    global $weatherConfig;
    $weatherData = getWeatherData();
    
    $html = '<div class="weather-widget" id="weatherWidget">';
    $html .= '<div class="weather-location">' . htmlspecialchars($weatherConfig['city']) . '</div>';
    $html .= '<div class="weather-main">';
    $html .= '<div class="weather-icon" id="weatherIcon">' . $weatherData['icon'] . '</div>';
    $html .= '<div class="weather-temp" id="weatherTemp">' . htmlspecialchars($weatherData['temp']) . '</div>';
    $html .= '</div>';
    $html .= '<div class="weather-description" id="weatherDescription">' . htmlspecialchars($weatherData['description']) . '</div>';
    $html .= '<div class="weather-details">';
    $html .= '<div class="weather-detail">';
    $html .= '<span>Umidità</span>';
    $html .= '<span id="weatherHumidity">' . htmlspecialchars($weatherData['humidity']) . '</span>';
    $html .= '</div>';
    $html .= '<div class="weather-detail">';
    $html .= '<span>Vento</span>';
    $html .= '<span id="weatherWind">' . htmlspecialchars($weatherData['wind']) . '</span>';
    $html .= '</div>';
    $html .= '<div class="weather-detail">';
    $html .= '<span>Pressione</span>';
    $html .= '<span id="weatherPressure">' . htmlspecialchars($weatherData['pressure']) . '</span>';
    $html .= '</div>';
    $html .= '<div class="weather-detail">';
    $html .= '<span>Visibilità</span>';
    $html .= '<span id="weatherVisibility">' . htmlspecialchars($weatherData['visibility']) . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// Endpoint AJAX per aggiornare i dati meteo
//if (isset($_GET['ajax']) && $_GET['ajax'] === 'weather') {
//    header('Content-Type: application/json; charset=utf-8');
//    echo json_encode(getWeatherData(), JSON_UNESCAPED_UNICODE);
//    exit;
//}
?>