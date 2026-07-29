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
	'ACP_TIMEOUT_TITLE'           => 'Timeout Extension',
	'ACP_TIMEOUT_SETTINGS'        => 'Settings',
	'ACP_TIMEOUT_MANAGE'          => 'Manage Timeouts',
	
	// Settings page
	'ACP_TIMEOUT_SETTINGS_EXPLAIN' => 'Here you can configure the options for the Timeout extension.',
	'TIMEOUT_ENABLE'              => 'Enable Timeout',
	'TIMEOUT_ENABLE_EXPLAIN'      => 'Enable or disable the timeout functionality globally.',
	'TIMEOUT_NOTIFY_USER'         => 'Notify User',
	'TIMEOUT_NOTIFY_USER_EXPLAIN' => 'Send notification to users when they are placed in timeout.',
	'TIMEOUT_MAX_DURATION'        => 'Maximum Timeout Duration',
	'TIMEOUT_MAX_DURATION_EXPLAIN' => 'Maximum duration (in minutes) that a user can be placed in timeout. Allowed range: 1 to 43200 minutes (30 days). Default is 1440 (24 hours).',
	'TIMEOUT_MAX_DURATION_INVALID' => 'The maximum timeout duration must be between 1 and 43200 minutes (30 days).',
	'TIMEOUT_LOG'                 => 'Log Timeout Actions',
	'TIMEOUT_LOG_EXPLAIN'         => 'Record moderator timeout actions in the moderator log.',
	'TIMEOUT_SETTINGS_UPDATED'    => 'Timeout settings have been updated successfully.',
	
	// Manage page
	'ACP_TIMEOUT_MANAGE_EXPLAIN'  => 'Here you can manage all active and past timeouts.',
	'TIMEOUT_STATUS_ACTIVE'       => 'Active',
	'TIMEOUT_STATUS_EXPIRED'      => 'Expired',
	'NO_TIMEOUTS'                => 'No timeouts found',
	'ACP_TIMEOUT_SETTINGS_SAVED'  => 'Timeout settings have been saved successfully.',
	'TIMEOUT_FILTER'              => 'Filter timeouts',
	'TIMEOUT_NO_RECORDS'          => 'No timeout records found.',
	'TIMEOUT_USER'                => 'User',
	'TIMEOUT_START'               => 'Start Time',
	'TIMEOUT_END'                 => 'End Time',
	'TIMEOUT_STATUS'              => 'Status',
	'TIMEOUT_MOD'                 => 'Issued By',
	'TIMEOUT_REASON'              => 'Reason',
	'TIMEOUT_ACTIONS'             => 'Actions',
	'TIMEOUT_DELETE'              => 'Delete',
	'TIMEOUT_END_NOW'             => 'End Now',
	'TIMEOUT_VIEW_USER'           => 'View User',
	'TIMEOUT_PURGE'               => 'Permanently Remove',
	'TIMEOUT_CONFIRM_DELETE'      => 'Are you sure you want to delete this timeout record?',
	'TIMEOUT_CONFIRM_END'         => 'Are you sure you want to end this timeout?',
	'TIMEOUT_DELETED'             => 'Timeout record has been deleted successfully.',
	'TIMEOUT_ENDED'               => 'User timeout has been ended successfully.',
	'ACP_TIMEOUT_PURGED'          => 'Timeout record has been permanently removed successfully.',
	'ACP_TIMEOUT_NOT_FOUND'       => 'The requested timeout record was not found.',
	'TIMEOUT_STATUS_ACTIVE'       => 'Active',
	'TIMEOUT_STATUS_INACTIVE'     => 'Inactive',
	'TIMEOUT_FILTER'              => 'Filter Timeouts',
	'TIMEOUT_FILTER_ACTIVE'       => 'Show only active timeouts',
	'TIMEOUT_FILTER_INACTIVE'     => 'Show only inactive timeouts',
	'TIMEOUT_FILTER_ALL'          => 'Show all timeouts',
	'L_TIMEOUT_USER_CANNOT_INTERACT' => 'The user in timeout mode cannot interact until the time expires.',
]);
