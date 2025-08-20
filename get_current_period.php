<?php
// File: getCurrentPeriod.php
// Endpoint per restituire il periodo scolastico corrente

// Funzione per determinare il periodo corrente
function getCurrentPeriod() {
    date_default_timezone_set('Europe/Rome');
    $hour = (int)date('H');
    $minute = (int)date('i');
    $dayOfWeek = (int)date('N'); // 1 = lunedì, 7 = domenica
    $currentTime = $hour * 60 + $minute; // Tempo in minuti dall'inizio del giorno
    if ($dayOfWeek >=6) {
        return 'NESSUNA LEZIONE';
    }
    // Definizione degli orari scolastici base (in minuti dall'inizio del giorno)
    $periods = [
        ['start' => 8 * 60, 'end' => 8 * 60 + 50, 'name' => 'PRIMA ORA'],
        ['start' => 8 * 60 + 50, 'end' => 9 * 60 + 45, 'name' => 'SECONDA ORA'],
        ['start' => 8 * 60 + 45, 'end' => 9 * 60 + 55, 'name' => 'PRIMO INTERVALLO'],
        ['start' => 9 * 60 + 55, 'end' => 10 * 60 + 50, 'name' => 'TERZA ORA'],
        ['start' => 10 * 60 + 50, 'end' => 11 * 60 + 45, 'name' => 'QUARTA ORA'],
        ['start' => 11 * 60 + 45, 'end' => 11 * 60 + 55, 'name' => 'SECONDO INTERVALLO'],
        ['start' => 11 * 60 + 55, 'end' => 12 * 60 + 50, 'name' => 'QUINTA ORA'],
        ['start' => 12 * 60 + 50, 'end' => 13 * 60 + 40, 'name' => 'SESTA ORA']
    ];
    
    // Aggiungi ore pomeridiane solo il mercoledì (giorno 3)
    if ($dayOfWeek === 3) {
        $periods = array_merge($periods, [
            ['start' => 14 * 60, 'end' => 14 * 60 + 50, 'name' => 'SETTIMA ORA'],
            ['start' => 14 * 60 + 50, 'end' => 15 * 60 + 40, 'name' => 'OTTAVA ORA'],
        ]);
    }
    
    // Controlla in quale periodo siamo
    foreach ($periods as $period) {
        if ($currentTime >= $period['start'] && $currentTime < $period['end']) {
            return $period['name'];
        }
    }
    
    // Gestione degli stati fuori orario
    if ($currentTime < 8 * 60) {
        return 'PRIMA DELL\'INIZIO LEZIONI';
    } elseif ($currentTime >= 13 * 60 + 40 && $currentTime < 14 * 60) {
        // Pausa pranzo solo il mercoledì
        if ($dayOfWeek === 3) {
            return 'PAUSA PRANZO';
        } else {
            return 'LEZIONI TERMINATE';
        }
    } elseif ($currentTime >= 15 * 60 + 40) {
        return 'LEZIONI TERMINATE';
    }  elseif ($dayOfWeek !== 3 && $currentTime > 13 * 60 + 40) {
        // Dopo le lezioni negli altri giorni
        return 'LEZIONI TERMINATE';
    } else {
        return 'INTERVALLO';
    }
}

?>