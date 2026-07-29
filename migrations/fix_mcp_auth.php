<?php

namespace marcozp\timeout\migrations;

class fix_mcp_auth extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\marcozp\timeout\migrations\install_extension'];
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

		return $row && $row['module_auth'] === 'm_timeout';
	}

	public function update_data()
	{
		return [
			['custom', [[$this, 'fix_module_auth']]],
		];
	}

	public function fix_module_auth()
	{
		$sql = 'UPDATE ' . $this->table_prefix . "modules
				SET module_auth = 'm_timeout'
				WHERE module_basename = '\\\\marcozp\\\\timeout\\\\mcp\\\\main_module'
				AND module_class = 'mcp'";
		$this->db->sql_query($sql);
	}
}
