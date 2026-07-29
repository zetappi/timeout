<?php

namespace marcozp\timeout\migrations;

class fix_mcp_auth_prefix extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\marcozp\timeout\migrations\fix_mcp_auth'];
	}

	public function effectively_installed()
	{
		$sql = 'SELECT module_auth FROM ' . $this->table_prefix . "modules
				WHERE module_basename = '\\\\marcozp\\\\timeout\\\\mcp\\\\main_module'
				AND module_class = 'mcp'
				AND module_mode = 'main'
				LIMIT 1";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row && $row['module_auth'] === 'acl_m_timeout';
	}

	public function update_data()
	{
		return [
			['custom', [[$this, 'fix_module_auth_prefix']]],
		];
	}

	public function fix_module_auth_prefix()
	{
		// phpBB's module_auth() evaluator only recognizes prefixed tokens
		// (acl_XXX, aclf_XXX, cfg_XXX, ...). A bare 'm_timeout' with no
		// prefix matches none of the valid_tokens patterns in
		// functions_module.php::module_auth() and is silently stripped to
		// an empty string before the eval(), which resolves to false —
		// causing "you must be logged in to moderate this forum" even
		// for users who do have the m_timeout permission.
		$sql = 'UPDATE ' . $this->table_prefix . "modules
				SET module_auth = 'acl_m_timeout'
				WHERE module_basename = '\\\\marcozp\\\\timeout\\\\mcp\\\\main_module'
				AND module_class = 'mcp'";
		$this->db->sql_query($sql);
	}

	public function revert_data()
	{
		return [
			['custom', [[$this, 'fix_module_auth_prefix']]],
		];
	}
}
