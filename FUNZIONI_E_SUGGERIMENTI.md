# Estensione Timeout per phpBB – Funzionalità e Suggerimenti di Espansione

## Descrizione Generale
L'estensione Timeout per phpBB permette ai moderatori di applicare, gestire e monitorare periodi di sospensione temporanea (timeout) agli utenti del forum. Offre strumenti avanzati per la moderazione, la gestione delle regole e la trasparenza delle azioni.

---

## Funzionalità Principali

### 1. Applicazione Timeout agli Utenti
- I moderatori possono mettere in timeout un utente specificando la durata e la motivazione.
- È possibile collegare il timeout a uno specifico post o discussione.
- I timeout non possono essere applicati a sé stessi, amministratori o altri moderatori.

### 2. Gestione Timeout Attivi
- Visualizzazione della lista dei timeout attualmente attivi.
- Dettagli su utente, motivazione, data di inizio e fine, moderatore che ha applicato la sanzione.

### 3. Storico Timeout
- Consultazione dello storico dei timeout ricevuti da ciascun utente.
- Ogni record mostra data, motivazione, post correlato e stato (attivo/scaduto).

### 4. Modifica e Rimozione Timeout
- I moderatori possono terminare anticipatamente un timeout attivo o eliminarne uno dallo storico.
- Conferma richiesta prima di azioni irreversibili.

### 5. Notifiche e Messaggi
- Possibilità di notificare l’utente quando viene applicato un timeout, con motivazione personalizzata.
- Messaggi di conferma e di errore chiari per ogni azione (es. durata non valida, utente non trovato, permessi insufficienti).

### 6. Permessi Granulari
- Solo i moderatori con permesso specifico possono applicare timeout.
- Gestione dei permessi tramite ACP (pannello di amministrazione).

### 7. Integrazione con Log Moderatori
- Ogni azione di timeout viene registrata nel log dei moderatori per trasparenza e tracciabilità.

### 8. Interfaccia Utente
- Pagine dedicate per la gestione, visualizzazione e storico dei timeout.
- Etichette, pulsanti e messaggi completamente localizzati (italiano e inglese).

### 9. API e Controlli AJAX
- Endpoint per verificare via AJAX se un utente è attualmente in timeout.

---

## Suggerimenti per Espansione Futura

1. **Timeout Progressivi**
   - Applicare durate crescenti ai timeout in base alla recidiva dell’utente.
2. **Motivi Predefiniti**
   - Offrire una lista di motivi standard selezionabili per velocizzare la moderazione.
3. **Reportistica Avanzata**
   - Statistiche su timeout applicati, utenti più sanzionati, motivi più frequenti.
4. **Integrazione con altre Estensioni**
   - Sincronizzazione con sistemi di warning, ban o reputazione.
5. **Notifiche Personalizzate**
   - Invio di notifiche via email o messaggi privati automatici.
6. **Timeout Parziali**
   - Limitare solo alcune funzionalità (es. postare, inviare PM) invece di un blocco totale.
7. **Gestione da Mobile**
   - Ottimizzare l’interfaccia per la moderazione da dispositivi mobili.
8. **Log Pubblico per Utenti**
   - Possibilità per l’utente di consultare lo storico dei propri timeout.
9. **Automazioni**
   - Timeout automatici su trigger specifici (es. superamento di infrazioni).

---

## Conclusione
L’estensione Timeout rappresenta uno strumento potente e flessibile per la moderazione su phpBB. L’implementazione delle funzionalità suggerite permetterebbe di renderla ancora più completa e adattabile alle esigenze delle varie community.
