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
		// No-op intenzionale. Ripristinare 'm_timeout' non avrebbe senso
		// (tornerebbe al valore che fix_mcp_auth aveva già corretto una
		// volta, non a uno stato "prima" valido) e chiamare di nuovo
		// fix_module_auth_prefix() (il bug originale in questo file)
		// eseguirebbe l'update in avanti invece di annullarlo. Il record
		// module_auth stesso viene comunque rimosso dal record module.add
		// corrispondente in install_extension.php quando phpBB inverte
		// automaticamente il suo update_data() durante il purge.
		return [];
	}
}
