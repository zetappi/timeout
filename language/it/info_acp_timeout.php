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
	// ACP Module
	'ACP_TIMEOUT_TITLE'           => 'Estensione Timeout',
	'ACP_TIMEOUT_SETTINGS'        => 'Impostazioni',
	'ACP_TIMEOUT_MANAGE'          => 'Gestisci Timeout',
	
	// Settings page
	'ACP_TIMEOUT_SETTINGS_EXPLAIN' => 'Qui puoi configurare le opzioni per l\'estensione Timeout.',
	'TIMEOUT_ENABLE'              => 'Abilita Timeout',
	'TIMEOUT_ENABLE_EXPLAIN'      => 'Abilita o disabilita la funzionalità di timeout globalmente.',
	'TIMEOUT_NOTIFY_USER'         => 'Notifica Utente',
	'TIMEOUT_NOTIFY_USER_EXPLAIN' => 'Invia una notifica agli utenti quando vengono messi in timeout.',
	'TIMEOUT_MAX_DURATION'        => 'Durata Massima Timeout',
	'TIMEOUT_MAX_DURATION_EXPLAIN' => 'Durata massima (in minuti) per cui un utente può essere messo in timeout. Predefinito: 1440 (24 ore).',
	'TIMEOUT_LOG'                 => 'Registra Azioni Timeout',
	'TIMEOUT_LOG_EXPLAIN'         => 'Registra le azioni di timeout dei moderatori nel log dei moderatori.',
	'TIMEOUT_SETTINGS_UPDATED'    => 'Le impostazioni del timeout sono state aggiornate con successo.',
	
	// Manage page
	'ACP_TIMEOUT_MANAGE_EXPLAIN'  => 'Qui puoi gestire tutti i timeout attivi e passati.',
	'TIMEOUT_STATUS_ACTIVE'       => 'Attivo',
	'TIMEOUT_STATUS_EXPIRED'      => 'Scaduto',
	'NO_TIMEOUTS'                => 'Nessun timeout trovato',
	'ACP_TIMEOUT_SETTINGS_SAVED'  => 'Le impostazioni del timeout sono state salvate con successo.',
	'TIMEOUT_FILTER'              => 'Filtra timeout',
	'TIMEOUT_NO_RECORDS'          => 'Nessun record di timeout trovato.',
	'TIMEOUT_USER'                => 'Utente',
	'TIMEOUT_START'               => 'Ora Inizio',
	'TIMEOUT_END'                 => 'Ora Fine',
	'TIMEOUT_STATUS'              => 'Stato',
	'TIMEOUT_MOD'                 => 'Emesso Da',
	'TIMEOUT_REASON'              => 'Motivo',
	'TIMEOUT_ACTIONS'             => 'Azioni',
	'TIMEOUT_DELETE'              => 'Elimina',
	'TIMEOUT_END_NOW'             => 'Termina Ora',
	'TIMEOUT_VIEW_USER'           => 'Visualizza Utente',
	'TIMEOUT_PURGE'               => 'Rimuovi Definitivamente',
	'TIMEOUT_CONFIRM_DELETE'      => 'Sei sicuro di voler eliminare questo record di timeout?',
	'TIMEOUT_CONFIRM_END'         => 'Sei sicuro di voler terminare questo timeout?',
	'TIMEOUT_DELETED'             => 'Il record di timeout è stato eliminato con successo.',
	'TIMEOUT_ENDED'               => 'Il timeout dell\'utente è stato terminato con successo.',
	'ACP_TIMEOUT_PURGED'          => 'Il record di timeout è stato rimosso definitivamente con successo.',
	'ACP_TIMEOUT_NOT_FOUND'       => 'Il record di timeout richiesto non è stato trovato.',
	'TIMEOUT_STATUS_ACTIVE'       => 'Attivo',
	'TIMEOUT_STATUS_INACTIVE'     => 'Inattivo',
	'TIMEOUT_FILTER'              => 'Filtra Timeout',
	'TIMEOUT_FILTER_ACTIVE'       => 'Mostra solo timeout attivi',
	'TIMEOUT_FILTER_INACTIVE'     => 'Mostra solo timeout inattivi',
	'TIMEOUT_FILTER_ALL'          => 'Mostra tutti i timeout',
	'TIMEOUT_DAY'                 => 'giorni',
	'L_TIMEOUT_USER_CANNOT_INTERACT' => 'L’utente in modalità timeout non può interagire fino alla scadenza del tempo.',
]);
