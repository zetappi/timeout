<?php

namespace marcozp\timeout\mcp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\marcozp\timeout\mcp\main_module',
            'title'     => 'MCP_TIMEOUT_TITLE',
            'modes'     => [
                'main'      => [
                    'title' => 'MCP_TIMEOUT_MAIN',
                    'auth'  => 'aclf_m_timeout',
                    'cat'   => ['MCP_TIMEOUT_TITLE'],
                ],
                'active'    => [
                    'title' => 'MCP_TIMEOUT_ACTIVE',
                    'auth'  => 'aclf_m_timeout',
                    'cat'   => ['MCP_TIMEOUT_TITLE'],
                ],
                'history'   => [
                    'title' => 'MCP_TIMEOUT_HISTORY',
                    'auth'  => 'aclf_m_timeout',
                    'cat'   => ['MCP_TIMEOUT_TITLE'],
                ],
            ],
        ];
    }
}
