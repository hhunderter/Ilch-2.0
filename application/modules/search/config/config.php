<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Config;

class Config extends \Ilch\Config\Install
{
    public $config = [
        'key' => 'search',
        'icon_small' => 'fa-solid fa-magnifying-glass',
        'system_module' => true,
        'languages' => [
            'de_DE' => [
                'name' => 'Suche',
                'description' => 'Hier kannen das System durchsucht werden.',
            ],
            'en_EN' => [
                'name' => 'Search',
                'description' => 'Here the system can be searched.',
            ],
        ],
        'boxes' => [
            'search' => [
                'de_DE' => [
                    'name' => 'Suche'
                ],
                'en_EN' => [
                    'name' => 'Search'
                ]
            ]
        ]
    ];

    public function install()
    {
        $databaseConfig = new \Ilch\Config\Database($this->db());
        $databaseConfig->set('search_boxmodule', implode(',', ['forum']));

        $this->db()->queryMulti($this->getInstallSql());

        $this->db()->insert('search_result')
            ->columns(['name', 'version', 'url_controller', 'url_action', 'url_idkey'])
            ->values([
                ['forum', '1.34.1', 'showposts', 'index', 'topicid'],
                ['downloads', '1.13.3', 'index', 'showfile', 'id'],
                ['faq', '1.9.1', 'index', 'show', 'id']
            ])
            ->execute();
    }

    public function uninstall()
    {
        $this->db()->drop('search', true);
        $this->db()->drop('search_result', true);
    }

    public function getInstallSql(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `[prefix]_search` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(255) NOT NULL,
          `version` VARCHAR(100) NOT NULL,
          `url_controller` VARCHAR(100) NOT NULL,
          `url_action` VARCHAR(100) NOT NULL,
          `url_idkey` VARCHAR(100) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

        CREATE TABLE IF NOT EXISTS `[prefix]_search_result` (
          `uid` VARCHAR(255) NOT NULL,
          `search` VARCHAR(255) NOT NULL,
          `module` TEXT NOT NULL,
          `days` VARCHAR(255) NOT NULL,
          `result` TEXT NOT NULL,
          `dateCreated` DATETIME NOT NULL,
          PRIMARY KEY (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ';
    }

    public function getUpdate(string $installedVersion): string
    {
        switch ($installedVersion) {
            case "2.1.54":
                //First Version
        }

        return '"' . $this->config['key'] . '" Update-function executed.';
    }
}
