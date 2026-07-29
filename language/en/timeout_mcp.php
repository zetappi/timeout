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
	'MCP_TIMEOUT_TITLE'             => 'Timeout Management',
	'MCP_TIMEOUT_MAIN'              => 'Main',
	'MCP_TIMEOUT_ACTIVE'            => 'Active Timeouts',
	'MCP_TIMEOUT_HISTORY'           => 'Timeout History',

	// Form fields and messages
	'TIMEOUT_DURATION'              => 'Timeout Duration',
	'TIMEOUT_DURATION_EXPLAIN'      => 'Select the timeout duration from the list.',
	'DURATION_30MIN'                => '30 minutes',
	'DURATION_1H'                   => '1 hour',
	'DURATION_2H'                   => '2 hours',
	'DURATION_4H'                   => '4 hours',
	'DURATION_8H'                   => '8 hours',
	'DURATION_24H'                  => '24 hours',
	'DURATION_2D'                   => '2 days',
	'DURATION_7D'                   => '7 days',
	'DURATION_14D'                  => '14 days',
	'DURATION_30D'                  => '30 days',
	'TIMEOUT_REASON'                => 'Timeout Reason',
	'TIMEOUT_REASON_EXPLAIN'        => 'Enter a reason for this timeout that may be shown to the user.',
	'TIMEOUT_REASON_PRESET'         => 'Preset reason',
	'TIMEOUT_REASON_PRESET_CUSTOM'  => 'Custom',
	'TIMEOUT_REASON_PRESET_MOD_DECISION' => 'You have been placed in Timeout by moderator decision',
	'TIMEOUT_REASON_PRESET_OFFTOPIC' => 'You have been placed in Timeout for repeated Off Topic behavior',
	'TIMEOUT_NOTIFY_USER'           => 'Notify User',
	'TIMEOUT_NOTIFY_USER_EXPLAIN'   => 'Send a notification to the user informing them of the timeout.',
	'TIMEOUT_USER'                  => 'Username',
	'TIMEOUT_USER_EXPLAIN'          => 'Enter the username of the user to timeout.',
	'TIMEOUT_CONFIRM_APPLY'         => 'Apply a timeout of %2$s to user "%1$s"?',
	'USER_HISTORY'                  => 'User History',
	'BAN_HISTORY'                   => 'Ban Count',
	'WARNING_HISTORY'               => 'Warning Count',
	'RISK_INDEX'                    => 'Karma Factor',
	'SUGGESTED_DURATION'            => 'Effective Duration',
	'MAX_DURATION_ACP'              => 'Maximum Duration (ACP)',
	'HOUR'                          => 'Hour',
	'HOURS'                         => 'Hours',
	
	// Error messages
	'TIMEOUT_DURATION_INVALID'      => 'The timeout duration must be greater than zero.',
	'TIMEOUT_USER_NOT_SPECIFIED'    => 'No user was specified for the timeout.',
	'TIMEOUT_USER_NOT_FOUND'        => 'The specified user could not be found.',
	'TIMEOUT_USER_ALREADY_IN_TIMEOUT' => 'This user is already in timeout.',
	'TIMEOUT_ADMIN_MOD_EXEMPT'      => 'Administrators and moderators cannot be put in timeout.',
	'TIMEOUT_MAX_DURATION_EXCEEDED' => 'The timeout duration exceeds the maximum allowed (%d minutes).',
	
	// Success messages
	'TIMEOUT_APPLIED_SUCCESS'       => 'The timeout has been applied successfully.',
	'TIMEOUT_ADDED_SUCCESS'         => 'Timeout has been successfully applied to %s.',
	'TIMEOUT_REMOVED_SUCCESS'       => 'The timeout has been removed successfully.',

	// Buttons and UI elements
	'TIMEOUT_SUBMIT'                => 'Apply Timeout',
	'TIMEOUT_CANCEL'                => 'Cancel',
	'TIMEOUT_REMOVE'                => 'Remove Timeout',
	'TIMEOUT_VIEW_HISTORY'          => 'View Timeout History',
	'TIMEOUT_UNTIL'                 => 'User is in timeout until: %s',
	'TIMEOUT_EXPIRES_AT'            => 'Timeout Expires: %s',
	'TIMEOUT_STATUS'                => 'Timeout Status',
	'TIMEOUT_EXPIRED'               => 'Expired',
	'TIMEOUT_ACTIVE'                => 'Active',
	'TIMEOUT_MAX_ALLOWED'           => 'Maximum allowed',
]);
