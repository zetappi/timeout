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
	'TIMEOUT_USER'           => 'Timeout User',
	'TIMEOUT_USER_INFO'      => 'Timeout Information for %s',
	'TIMEOUT_DURATION'       => 'Timeout Duration',
	'TIMEOUT_REASON'         => 'Timeout Reason',
	'TIMEOUT_ACTIVE'         => 'Active Timeouts',
	'TIMEOUT_HISTORY'        => 'Timeout History',
	'TIMEOUT_ACTIONS'        => 'Actions',
	'TIMEOUT_END_NOW'        => 'End Timeout Now',
	'TIMEOUT_EDIT'           => 'Edit Timeout',
	'TIMEOUT_NEW_DURATION'   => 'New duration (from now)',
	'TIMEOUT_DELETE'         => 'Delete Timeout',
	'TIMEOUT_CONFIRM_END'    => 'Are you sure you want to end the timeout for user "%1$s"?',
	'TIMEOUT_CONFIRM_DELETE' => 'Are you sure you want to delete this timeout record?',
	'TIMEOUT_CONFIRM_EDIT'   => 'Set the timeout for "%1$s" to %2$s from now?',
	'TIMEOUT_EDIT_SUCCESS'   => 'The timeout duration has been updated successfully.',
	'TIMEOUT_ADDED'          => 'User has been placed in timeout successfully.',
	'TIMEOUT_ENDED'          => 'User timeout has been ended successfully.',
	'TIMEOUT_DELETED'        => 'Timeout record has been deleted successfully.',
	'TIMEOUT_INVALID_FORM'   => 'Invalid form data.',
	'TIMEOUT_INVALID_USER'   => 'Invalid user specified.',
	'TIMEOUT_INVALID_DURATION' => 'Invalid timeout duration.',
	'TIMEOUT_NOT_FOUND'      => 'Timeout record was not found.',
	'TIMEOUT_DISABLED'       => 'Timeout functionality is currently disabled.',
	'TIMEOUT_USER_NOT_FOUND' => 'User was not found.',
	'TIMEOUT_BUTTON_LABEL'   => 'Timeout User',
	'TIMEOUT_START'          => 'Start Date',
	'TIMEOUT_END'            => 'End Date',
	'TIMEOUT_REMAINING'      => 'Time Remaining',
	'TIMEOUT_MOD'            => 'Issued By',
	'TIMEOUT_STATUS'         => 'Status',
	'TIMEOUT_STATUS_ACTIVE'  => 'Active',
	'TIMEOUT_STATUS_INACTIVE' => 'Inactive',
	'TIMEOUT_POST_REFERENCE' => 'Related Post',
	'TIMEOUT_VIEW_POST'      => 'View Post',
	'TIMEOUT_NOTIFICATION'   => 'You have been placed in timeout until %1$s for the following reason: %2$s',
	'TIMEOUT_NOTIFY_USER'    => 'Notify user',
	'TIMEOUT_END'            => 'End Timeout',
	'TIMEOUT_PURGE'          => 'Delete from DB',
	'NOTIFICATION_TYPE_TIMEOUT' => 'Someone puts you in timeout',
	'NOTIFICATION_TIMEOUT_TITLE' => 'You have been placed in timeout',
	'NOTIFICATION_TIMEOUT_REFERENCE' => 'You are in timeout',
	'ACP_TIMEOUT_PURGED'     => 'The timeout record has been permanently deleted from the database.',
	'ACP_TIMEOUT_ENDED'      => 'The timeout has been ended successfully.',
	'ACP_TIMEOUT_DELETED'    => 'The timeout has been deleted successfully.',
	'ACP_TIMEOUT_NOT_FOUND'  => 'Timeout not found.',
	'ACP_TIMEOUT_GENERAL_SETTINGS' => 'Timeout General Settings',
	'MCP_TIMEOUT_TITLE'      => 'Timeout Management',
	'MCP_TIMEOUT_MAIN'       => 'Main',
	'MCP_TIMEOUT_ACTIVE'     => 'Active Timeouts',
	'MCP_TIMEOUT_HISTORY'    => 'Timeout History',
	'TIMEOUT_STATUS_EXPIRED' => 'Expired',
	'TIMEOUT_USER_INFO_TITLE' => 'User Timeout Information',
	'TIMEOUT_COUNT'          => 'Total Timeouts Received',
	'TIMEOUT_NO_HISTORY'     => 'No timeout history found for this user.',
	'TIMEOUT_USER_IS_IN_TIMEOUT' => 'This user is currently in timeout.',
	'TIMEOUT_TIME_UNITS'     => [
		'MINUTE' => ['1 minute', '%d minutes'],
		'HOUR'   => ['1 hour', '%d hours'],
		'DAY'    => ['1 day', '%d days'],
		'WEEK'   => ['1 week', '%d weeks'],
	],
	'TIMEOUT_MAX_DURATION_EXCEEDED' => 'The timeout duration exceeds the maximum allowed (%d minutes).',
	'TIMEOUT_CURRENT_INFO'   => 'You are in timeout until %s.',
	'TIMEOUT_RESTRICTION_MESSAGE' => 'You are currently in timeout and cannot post new content.',
	'TIMEOUT_USER_CANNOT_POST'     => 'You cannot post while you are in timeout.',
	'TIMEOUT_USER_CANNOT_QUOTE'    => 'You cannot quote messages while you are in timeout.',
	'TIMEOUT_USER_CANNOT_EDIT'     => 'You cannot edit messages while you are in timeout.',
	'TIMEOUT_USER_CANNOT_PM'       => 'You cannot send private messages while you are in timeout.',
	'TIMEOUT_USER_RESTRICTED'      => '<strong>Warning:</strong> Your account is currently in timeout. You cannot post new messages, reply, edit or send private messages until the timeout expires.',
	'TIMEOUT_LOG_ADDED' => '<strong>Timeout added</strong><br />» User: %1$s<br />» Reason: %2$s<br />» Expiry: %3$s',
	'TIMEOUT_LOG_ENDED' => '<strong>Timeout ended</strong><br />» User: %2$s',
	'TIMEOUT_LOG_PURGED' => '<strong>Timeout deleted</strong><br />» User: %2$s',
	'NO_ACTIVE_TIMEOUTS' => 'There are no active timeouts at the moment.',
	'NO_TIMEOUT_HISTORY' => 'No timeout records found.',
	'TIMEOUT_ADDED_SUCCESS' => 'Timeout has been added successfully.',
	'TIMEOUT_REMOVED_SUCCESS' => 'Timeout has been removed successfully.',
	'MINUTES' => 'Minutes',
	'TIMEOUT_BADGE_TEXT' => 'In timeout',
	'RISK_INDEX'             => 'Karma Factor',
	'RISK_INDEX_EXPLAIN'     => 'Calculated based on disciplinary history (0-100)',
	'TIMEOUT_USER_CANNOT_INTERACT' => 'The user in timeout mode cannot interact until the time expires.',
]);
