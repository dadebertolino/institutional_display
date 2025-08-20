# institutional_display
Display istituzionale per scuole collegabile al sito wordpress degli istituti scolastici

Negli Istituti Scolastici è spesso reperibile hardware vecchio o di recupero che deve essere smaltito.
Allo stesso tempo si verifica spesso la necessità di fornire all'utenza della scuola delle informazioni relative agli orari o altro.

Ho pertanto realizzato un mini progetto per la visualizzazione di un display istituzionale composto da una manciata di file.
Questi file possono essere adattati al bisogno, e scaricati su un sito web. 

La scuola non deve fare altro che recuperare un vecchio PC e collegarlo ad un televisore e poi far leggere la pagina web del display istituzionale.

Consiglio di utilizzare Chrome come visualizzatore in versione Kiosk. 

Per attivare modalità Kiosk dovete procedere come segue:

Chiudete Google Chrome, assicurandovi che non sia in esecuzione in background, potete disattivare nelle impostazioni del browser tramite Impostazioni Avanzate, l’opzione Continua a eseguire le applicazioni dopo la chiusura di Chrome
Cliccate con il tasto destro del mouse sull’icona di Chrome
Cliccate su Proprietà
Copiare il percorso di Chrome
Creare un nuovo collegamento sul desktop.
In Destinazione incollate il percorso di Chrome, quindi spazio e digitate alla fine del percorso –kiosk indirizzo_sito_web (sostituendo naturalmente sito web con il sito che volete consultare a schermo intero), quindi il risultato sarà “C:Program Files (x86)GoogleChromeApplicationchrome.exe” –kiosk PercorsoPaginaPhpDisplayIstituzionale
Cliccate su Applica per apportare i cambiamenti

Per la personalizzazione è possibile cambiare il nome dell'istituto e il logo nel file institutional_display.php.
Ho anche inserito un widget per la visualizzazione del meteo attuale collegato al servizio OpenWeatherMap.org
Creare un account gratuito e poi creare una Api Key e caricarla nel file forecast.php insieme al nome della città.

Utilizzabile liberamente. 
