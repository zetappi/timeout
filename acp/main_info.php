<?php

namespace marcozp\timeout\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\marcozp\timeout\acp\main_module',
            'title'     => 'ACP_TIMEOUT_TITLE',
            'modes'    => [
                'settings'    => [
                    'title' => 'ACP_TIMEOUT_SETTINGS',
                    'auth'  => 'ext_marcozp/timeout && acl_a_board',
                    'cat'   => ['ACP_TIMEOUT_TITLE']
                ],
                'manage'    => [
                    'title' => 'ACP_TIMEOUT_MANAGE',
                    'auth'  => 'ext_marcozp/timeout && acl_a_board',
                    'cat'   => ['ACP_TIMEOUT_TITLE']
                ],
            ],
        ];
    }
}