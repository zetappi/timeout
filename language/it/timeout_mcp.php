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
	// MCP Module
	'MCP_TIMEOUT_TITLE'             => 'Gestione Timeout',
	'MCP_TIMEOUT_MAIN'              => 'Principale',
	'MCP_TIMEOUT_ACTIVE'            => 'Timeout Attivi',
	'MCP_TIMEOUT_HISTORY'           => 'Storico Timeout',

	// Form fields and messages
	'TIMEOUT_DURATION'              => 'Durata Timeout',
	'TIMEOUT_DURATION_EXPLAIN'      => 'Inserisci il numero di minuti per il timeout.',
	'TIMEOUT_REASON'                => 'Motivo Timeout',
	'TIMEOUT_REASON_EXPLAIN'        => 'Inserisci un motivo per questo timeout che potrebbe essere mostrato all\'utente.',
	'TIMEOUT_NOTIFY_USER'           => 'Notifica Utente',
	'TIMEOUT_NOTIFY_USER_EXPLAIN'   => 'Invia una notifica all\'utente informandolo del timeout.',
	'TIMEOUT_USER'                  => 'Nome Utente',
	'TIMEOUT_USER_EXPLAIN'          => 'Inserisci il nome utente della persona a cui applicare il timeout.',
	'RISK_INDEX'                    => 'Fattore Karma',
	'SUGGESTED_DURATION'            => 'Durata Effettiva',
	'MAX_DURATION_ACP'              => 'Durata Massima (ACP)',
	'HOUR'                          => 'Ora',
	'HOURS'                         => 'Ore',
	
	// Error messages
	'TIMEOUT_DURATION_INVALID'      => 'La durata del timeout deve essere maggiore di zero.',
	'TIMEOUT_USER_NOT_SPECIFIED'    => 'Nessun utente è stato specificato per il timeout.',
	'TIMEOUT_USER_NOT_FOUND'        => 'L\'utente specificato non è stato trovato.',
	'TIMEOUT_USER_ALREADY_IN_TIMEOUT' => 'Questo utente è già in timeout.',
	'TIMEOUT_ADMIN_MOD_EXEMPT'      => 'Amministratori e moderatori non possono essere messi in timeout.',
	'TIMEOUT_MAX_DURATION_EXCEEDED' => 'La durata del timeout supera il massimo consentito (%d minuti).',
	
	// Success messages
	'TIMEOUT_APPLIED_SUCCESS'       => 'Il timeout è stato applicato con successo.',
	'TIMEOUT_ADDED_SUCCESS'         => 'Il timeout è stato applicato con successo a %s.',
	'TIMEOUT_REMOVED_SUCCESS'       => 'Il timeout è stato rimosso con successo.',

	// Buttons and UI elements
	'TIMEOUT_SUBMIT'                => 'Applica Timeout',
	'TIMEOUT_CANCEL'                => 'Annulla',
	'TIMEOUT_REMOVE'                => 'Rimuovi Timeout',
	'TIMEOUT_VIEW_HISTORY'          => 'Visualizza Cronologia Timeout',
	'TIMEOUT_UNTIL'                 => 'L\'utente è in timeout fino a: %s',
	'TIMEOUT_EXPIRES_AT'            => 'Scadenza Timeout: %s',
	'TIMEOUT_STATUS'                => 'Stato Timeout',
	'TIMEOUT_EXPIRED'               => 'Scaduto',
	'TIMEOUT_ACTIVE'                => 'Attivo',
	'TIMEOUT_MAX_ALLOWED'           => 'Massimo consentito',
]);
