<?php

namespace marcozp\timeout\migrations;

class update_timeout_post_id_type extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        // Controlleremo se la colonna post_id è già di tipo BINT
        return false;
    }

    static public function depends_on()
    {
        return ['\marcozp\timeout\migrations\install_extension'];
    }

    public function update_schema()
    {
        return [
            'change_columns' => [
                $this->table_prefix . 'user_timeouts' => [
                    'post_id' => ['BINT', 0],  // Usiamo BINT (BIGINT) invece di UINT
                    'topic_id' => ['BINT', 0], // Aggiorniamo anche topic_id per consistenza
                ],
            ],
        ];
    }
}
