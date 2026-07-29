<?php

namespace marcozp\timeout\migrations;

class update_user_timeout_fields extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_column_exists(USERS_TABLE, 'user_timeout_end');
    }

    public static function depends_on()
    {
        return ['\marcozp\timeout\migrations\install_extension'];
    }

    public function update_schema()
    {
        return [
            'add_columns' => [
                USERS_TABLE => [
                    'user_timeout_start' => ['INT:11', 0],
                    'user_timeout_end'   => ['INT:11', 0],
                ],
                SESSIONS_TABLE => [
                    'session_timeout_start' => ['INT:11', 0],
                    'session_timeout_end'   => ['INT:11', 0],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns' => [
                USERS_TABLE => [
                    'user_timeout_start',
                    'user_timeout_end',
                ],
                SESSIONS_TABLE => [
                    'session_timeout_start',
                    'session_timeout_end',
                ],
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.update', ['timeout_version', '1.0.1']],
        ];
    }
}
