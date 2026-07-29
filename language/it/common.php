<?php
/**
 *
 * Timeout extension for phpBB 3.3.x.
 * @copyright (c) marcozp
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, [
	'TIMEOUT_TITLE'          => 'Timeout',
	'TIMEOUT_USER'           => 'Inserisci Utente in Timeout',
	'TIMEOUT_USER_INFO'      => 'Informazioni Timeout per %s',
	'TIMEOUT_DURATION'       => 'Durata Timeout',
	'TIMEOUT_REASON'         => 'Motivo del Timeout',
	'TIMEOUT_ACTIVE'         => 'Timeout Attivi',
	'TIMEOUT_HISTORY'        => 'Storico Timeout',
	'TIMEOUT_ACTIONS'        => 'Azioni',
	'TIMEOUT_END_NOW'        => 'Interrompi',
	'TIMEOUT_END'            => 'Termina Timeout',
	'TIMEOUT_EDIT'           => 'Modifica Timeout',
	'TIMEOUT_NEW_DURATION'   => 'Nuova durata (da adesso)',
	'TIMEOUT_DELETE'         => 'Elimina Timeout',
	'TIMEOUT_PURGE'          => 'Elimina dal DB',
	'TIMEOUT_CONFIRM_END'    => 'Sei sicuro di voler terminare il timeout dell’utente «%1$s»?',
	'TIMEOUT_CONFIRM_DELETE' => 'Sei sicuro di voler eliminare questo record di timeout?',
	'TIMEOUT_CONFIRM_EDIT'   => 'Impostare la durata del timeout di «%1$s» a %2$s da adesso?',
	'TIMEOUT_EDIT_SUCCESS'   => 'La durata del timeout è stata aggiornata con successo.',
	'TIMEOUT_ADDED'          => 'L\'utente è stato messo in timeout con successo.',
	'TIMEOUT_ENDED'          => 'Il timeout dell\'utente è stato terminato con successo.',
	'TIMEOUT_DELETED'        => 'Il record di timeout è stato eliminato con successo.',
	'ACP_TIMEOUT_PURGED'     => 'Il record di timeout è stato eliminato definitivamente dal database.',
	'TIMEOUT_INVALID_FORM'   => 'Dati del modulo non validi.',
	'TIMEOUT_INVALID_USER'   => 'Utente specificato non valido.',
	'TIMEOUT_INVALID_DURATION' => 'Durata del timeout non valida.',
	'TIMEOUT_NOT_FOUND'      => 'Record di timeout non trovato.',
	'TIMEOUT_DISABLED'       => 'La funzionalità di timeout è attualmente disabilitata.',
	'TIMEOUT_USER_NOT_FOUND' => 'Utente non trovato.',
	'TIMEOUT_BUTTON_LABEL'   => 'Timeout Utente',
	'TIMEOUT_START'          => 'Data Inizio',
	'TIMEOUT_END'            => 'Data Fine',
	'TIMEOUT_REMAINING'      => 'Tempo Rimanente',
	'TIMEOUT_MOD'            => 'Emesso Da',
	'TIMEOUT_STATUS'         => 'Stato',
	'TIMEOUT_STATUS_ACTIVE'  => 'Attivo',
	'TIMEOUT_USER_IS_IN_TIMEOUT' => 'L\'utente è attualmente in timeout',
	'TIMEOUT_USER_CANNOT_POST' => 'Non puoi inviare messaggi mentre sei in timeout.',
	'TIMEOUT_USER_CANNOT_QUOTE' => 'Non puoi citare messaggi mentre sei in timeout.',
	'TIMEOUT_USER_CANNOT_EDIT' => 'Non puoi modificare messaggi mentre sei in timeout.',
	'TIMEOUT_USER_CANNOT_PM' => 'Non puoi inviare messaggi privati mentre sei in timeout.',
	'TIMEOUT_USER_RESTRICTED' => '<strong>AVVISO DELLA MODERAZIONE: </strong> Non puoi pubblicare nuovi messaggi, rispondere, modificare o inviare messaggi privati fino alla scadenza del Timeout.',
	'TIMEOUT_LOG_ADDED' => '<strong>Timeout aggiunto</strong><br />» Utente: %1$s<br />» Motivo: %2$s<br />» Scadenza: %3$s',
	'TIMEOUT_LOG_ENDED' => '<strong>Timeout terminato</strong><br />» Utente: %2$s',
	'TIMEOUT_LOG_PURGED' => '<strong>Timeout eliminato</strong><br />» Utente: %2$s',
	'ACP_TIMEOUT_ENDED'      => 'Il timeout è stato terminato con successo.',
	'ACP_TIMEOUT_DELETED'    => 'Il timeout è stato eliminato con successo.',
	'ACP_TIMEOUT_NOT_FOUND'  => 'Timeout non trovato.',
	'ACP_TIMEOUT_GENERAL_SETTINGS' => 'Impostazioni Generali Timeout',
	'MCP_TIMEOUT_TITLE'      => 'Gestione Timeout',
	'MCP_TIMEOUT_MAIN'       => 'Principale',
	'MCP_TIMEOUT_ACTIVE'     => 'Timeout Attivi',
	'MCP_TIMEOUT_HISTORY'    => 'Storico Timeout',
	'TIMEOUT_STATUS_INACTIVE' => 'Inattivo',
	'TIMEOUT_STATUS_EXPIRED' => 'Scaduto',
	'TIMEOUT_POST' => 'Post collegato',
	'TIMEOUT_POST_EXPLAIN' => 'Post che ha causato il timeout',
	'TIMEOUT_POST_REFERENCE' => 'Post Correlato',
	'TIMEOUT_VIEW_POST'      => 'Visualizza Post',
	'TIMEOUT_NOTIFICATION'   => 'Sei stato messo in timeout fino al %1$s per il seguente motivo: %2$s',
	'TIMEOUT_CURRENT_INFO'   => 'Sei in timeout fino al %s.',
	'TIMEOUT_NOTIFY_USER'    => 'Notifica utente',
	'TIMEOUT_USER_INFO_TITLE' => 'Informazioni Timeout Utente',
	'TIMEOUT_COUNT'          => 'Totale Timeout Ricevuti',
	'TIMEOUT_NO_HISTORY'     => 'Nessuno storico di timeout trovato per questo utente.',
	'TIMEOUT_USER_IS_IN_TIMEOUT' => 'Questo utente è attualmente inserito in timeout.',
	'TIMEOUT_TIME_UNITS'     => [
		'MINUTE' => ['1 minuto', '%d minuti'],
		'HOUR'   => ['1 ora', '%d ore'],
		'DAY'    => ['1 giorno', '%d giorni'],
		'WEEK'   => ['1 settimana', '%d settimane'],
	],
	'TIMEOUT_MAX_DURATION_EXCEEDED' => 'La durata del timeout supera il massimo consentito (%d minuti).',
	'TIMEOUT_CURRENT_INFO'   => 'Sei in timeout fino al %s.',
	'TIMEOUT_RESTRICTION_MESSAGE' => 'Sei attualmente in timeout e non puoi pubblicare nuovi contenuti.',
	'NO_ACTIVE_TIMEOUTS' => 'Non ci sono timeout attivi al momento.',
	'NO_TIMEOUT_HISTORY' => 'Nessun record di timeout trovato.',
	'TIMEOUT_ADDED_SUCCESS' => 'Il timeout è stato aggiunto con successo.',
	'TIMEOUT_REMOVED_SUCCESS' => 'Il timeout è stato rimosso con successo.',
	'MINUTES' => 'Minuti',
	'TIMEOUT_BADGE_TEXT' => 'In timeout',
	'BAN_HISTORY'            => 'Ban totali',
	'WARNING_HISTORY'        => 'Warning totali',
	'USER_HISTORY'           => 'Cronologia Utente',
	'TIMEOUT_TOTAL'          => 'Timeout totali',
	'RISK_INDEX'             => 'Fattore Karma',
	'RISK_INDEX_EXPLAIN'     => 'Calcolato in base alla storia disciplinare (0-100)',
	'TIMEOUT_USER_CANNOT_INTERACT'=> 'L’utente in modalità timeout non può interagire fino alla scadenza del tempo.',
]);
