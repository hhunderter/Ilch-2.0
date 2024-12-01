<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Error\Config;

class Config extends \Ilch\Config\Install
{
    public $config = [
        'key' => 'error',
        'system_module' => true
    ];

    public function install()
    {
    }

    public function getInstallSql()
    {
    }

    public function getUpdate(string $installedVersion)
    {
    }
}
