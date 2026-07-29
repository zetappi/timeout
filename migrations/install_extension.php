<?php

namespace marcozp\timeout\migrations;

class install_extension extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['timeout_version']) && version_compare($this->config['timeout_version'], '1.0.0', '>=');
    }

    public static function depends_on()
    {
        return [];
    }

    public function update_schema()
    {
        return [
            'add_tables' => [
                $this->table_prefix . 'user_timeouts' => [
                    'COLUMNS' => [
                        'timeout_id'     => ['UINT', null, 'auto_increment'],
                        'user_id'        => ['UINT', 0],
                        'mod_user_id'    => ['UINT', 0],
                        'timeout_start'  => ['INT:11', 0],
                        'timeout_end'    => ['INT:11', 0],
                        'timeout_reason' => ['TEXT', ''],
                        'timeout_status' => ['TINT:1', 1], // 1 = attivo, 0 = terminato
                        'post_id'        => ['BINT', 0], // post che ha causato il timeout (opzionale) - BINT per supportare ID molto grandi
                        'topic_id'       => ['BINT', 0], // topic che ha causato il timeout (opzionale) - BINT per supportare ID molto grandi
                    ],
                    'PRIMARY_KEY' => 'timeout_id',
                    'KEYS' => [
                        'user_id' => ['INDEX', 'user_id'],
                        'timeout_end' => ['INDEX', 'timeout_end'],
                        'timeout_status' => ['INDEX', 'timeout_status'],
                    ],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables' => [$this->table_prefix . 'user_timeouts'],
        ];
    }

    public function update_data()
    {
        return [
            // Aggiungi impostazioni di base
            ['config.add', ['timeout_enable', 1]],
            ['config.add', ['timeout_notify_user', 1]],
            ['config.add', ['timeout_max_duration', 1440]], // 24 ore in minuti
            ['config.add', ['timeout_log', 1]],
            ['config.add', ['timeout_version', '1.0.0']],
            
            // Aggiungi permessi
            ['permission.add', ['m_timeout', true]], // permesso moderatore
            ['permission.add', ['a_timeout', true]], // permesso admin
            
            // Assegna permessi ai ruoli
            ['permission.permission_set', ['ROLE_MOD_FULL', 'm_timeout', 'role']],
            ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_timeout', 'role']],
            
            // Aggiungi moduli ACP
            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_TIMEOUT_TITLE'
            ]],
            ['module.add', [
                'acp',
                'ACP_TIMEOUT_TITLE',
                [
                    'module_basename'   => '\marcozp\timeout\acp\main_module',
                    'modes'             => ['settings', 'manage'],
                ],
            ]],
            
            // Aggiungi moduli MCP
            ['module.add', [
                'mcp',
                0,
                'MCP_TIMEOUT_TITLE'
            ]],
            ['module.add', [
                'mcp',
                'MCP_TIMEOUT_TITLE',
                [
                    'module_basename'   => '\marcozp\timeout\mcp\main_module',
                    'modes'             => ['main', 'active', 'history'],
                ],
            ]],
        ];
    }

    // Nota: nessun revert_data() esplicito qui. Il Migrator core di phpBB
    // (phpbb/db/migrator.php::revert_do()) combina automaticamente
    // reverse_update_data(update_data()) con un eventuale revert_data()
    // esplicito - ogni step config.add/permission.add/permission.permission_set/
    // module.add in update_data() viene già invertito automaticamente
    // (rimozione config, permesso, e disassegnazione dai ruoli). Aggiungere
    // qui un revert_data() che rifà manualmente le stesse rimozioni
    // duplicherebbe il lavoro del reverse automatico durante un purge reale.
}