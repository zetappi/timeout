# User Timeout Extension
VERSIONE PHPBB3.3.X >>
Autore: Marco Zeppa
email: marcozp@gmail.com
(realizzato per https://forum.carlozampa.com)
## Overview
L'estensione User Timeout permette ai moderatori e agli amministratori di mettere temporaneamente in timeout gli utenti, impedendo loro di postare nuovi contenuti sul forum per un periodo di tempo definito. L'estensione offre un'interfaccia completa per la gestione dei timeout, con supporto multilingua e funzionalità avanzate di filtro e gestione.

## Caratteristiche

### Funzionalità di Base
- Impostare timeout per utenti con durata personalizzabile
- Visualizzare indicatori di utenti in timeout nei post
- Gestire tutti i timeout dal pannello di controllo del moderatore (MCP)
- Impostazioni configurabili dal pannello di amministrazione (ACP)
- Supporto completo per notifiche agli utenti
- Storico dei timeout per ogni utente

### Funzionalità Avanzate

#### Interfaccia MCP Migliorata
- **Selezione durata con radio button**: Invece di un campo numerico, sono disponibili opzioni predefinite (15, 30, 60, 120, 180 minuti) più l'opzione per il valore massimo configurato nell'ACP
- **Valore predefinito**: La durata predefinita è impostata a 60 minuti
- **Validazione durata massima**: Impedisce ai moderatori di impostare timeout superiori al limite massimo configurato nell'ACP
- **Messaggi di errore localizzati**: Tutti i messaggi di errore e di successo sono completamente localizzati

#### Gestione Timeout nell'ACP
- **Filtro per stato**: Possibilità di filtrare i timeout per visualizzare solo quelli attivi o scaduti
- **Filtro per username**: Ricerca timeout per nome utente
- **Azioni sui timeout**: 
  - Terminare immediatamente un timeout attivo
  - Rimuovere definitivamente un record di timeout
- **Visualizzazione stato**: Indicatori visivi per distinguere facilmente i timeout attivi da quelli scaduti

## Installazione
1. Scaricare l'archivio dell'estensione
2. Decomprimere nella cartella `/ext/marcozp/timeout/`
3. Andare in ACP -> Personalizzazioni -> Gestisci estensioni
4. Trovare l'estensione "User Timeout" e cliccare su "Abilita"

## Configurazione

### Impostazioni ACP
Dopo l'installazione, è possibile configurare l'estensione tramite il pannello di amministrazione:

1. Navigare a `ACP -> Estensioni -> Timeout -> Impostazioni`
2. Configurare le seguenti opzioni:
   - **Abilita Timeout**: Attiva o disattiva globalmente la funzionalità di timeout
   - **Notifica Utente**: Invia una notifica agli utenti quando vengono messi in timeout
   - **Durata Massima Timeout**: Imposta il limite massimo (in minuti) per la durata di un timeout
   - **Registra Azioni Timeout**: Registra le azioni di timeout nel log dei moderatori

### Utilizzo del Modulo MCP
I moderatori con i permessi appropriati possono gestire i timeout tramite il pannello di controllo del moderatore:

1. Navigare a `MCP -> Timeout`
2. Per assegnare un nuovo timeout:
   - Cercare un utente tramite il suo nome utente
   - Selezionare la durata del timeout dai radio button disponibili
   - Specificare il motivo del timeout
   - Cliccare su "Invia"

### Gestione Timeout nell'ACP
Gli amministratori possono gestire tutti i timeout dal pannello di amministrazione:

1. Navigare a `ACP -> Estensioni -> Timeout -> Gestisci Timeout`
2. Utilizzare i filtri disponibili per visualizzare i timeout:
   - Filtrare per stato (Attivo/Scaduto)
   - Filtrare per nome utente
3. Azioni disponibili:
   - **Termina Ora**: Termina immediatamente un timeout attivo
   - **Rimuovi Definitivamente**: Elimina completamente il record di timeout dal database

## Permessi
Dopo l'installazione, è possibile configurare i permessi utilizzando i seguenti ruoli:
- `m_timeout`: Permette ai moderatori di gestire i timeout
- `a_timeout`: Permette agli amministratori di configurare l'estensione

## Dettagli Tecnici

### Localizzazione
L'estensione è completamente localizzata e supporta le seguenti lingue:
- Italiano
- IngleseAutore: Marco Zeppa

email: marcozp@gmail.com

(realizzato per http://forum.popologiallorosso.com)

Tutti i messaggi di errore, notifiche e interfacce utente sono disponibili in entrambe le lingue.

### Validazione e Sicurezza
- **Validazione durata timeout**: Impedisce l'impostazione di timeout superiori al limite massimo configurato
- **Protezione CSRF**: Tutte le operazioni utilizzano token di sicurezza per prevenire attacchi CSRF
- **Sanitizzazione input**: Tutti gli input utente sono sanitizzati prima dell'uso
- **Gestione permessi**: Controllo rigoroso dei permessi per tutte le operazioni

### Database
L'estensione utilizza le seguenti tabelle:
- `phpbb_user_timeouts`: Memorizza tutti i record di timeout
- Modifica campi nelle tabelle standard di phpBB:
  - `USERS_TABLE`: Aggiunge campi per tracciare lo stato di timeout dell'utente
  - `SESSIONS_TABLE`: Aggiunge campi per gestire le sessioni degli utenti in timeout



## Supporto
Per supporto o segnalazione di bug, visitare il nostro [repository GitHub](https://github.com/marcozp/timeout).

## Licenza
GNU General Public License v2.0
