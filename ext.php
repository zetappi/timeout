<?php
/**
 * User Timeout extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Marco Zeppa
 * @license   GNU General Public License v2.0
 */

namespace marcozp\timeout;

class ext extends \phpbb\extension\base
{
    public function is_enableable(): bool
    {
        $config = $this->container->get('config');
        return \phpbb_version_compare($config['version'], '3.3.0', '>=');
    }
}
