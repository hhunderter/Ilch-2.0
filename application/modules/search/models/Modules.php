<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Models;

use Ilch\Accesses;
use Ilch\Request;
use Modules\Admin\Mappers\Module as ModuleMapper;
use Modules\Admin\Models\Module as ModuleModel;

class Modules extends \Ilch\Model
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * The Version.
     *
     * @var string
     */
    protected $version = '';

    /**
     * @var array
     */
    protected $url = [];

    /**
     * @var string
     */
    protected $urlIdKey = 'id';

    /**
     * @var boolean
     */
    protected $hasModul = false;

    /**
     * @var boolean
     */
    protected $hasAccess = false;

    /**
     * @var boolean
     */
    protected $hasVersion = false;

    /**
     * @var boolean
     */
    protected $checked = false;

    /**
     * @var string
     */
    protected $urlRoute = 'module';

    /**
     * @var string
     */
    protected $orderBy = '';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $entries
     * @return $this
     */
    public function setByArray(array $entries): Modules
    {
        if (isset($entries['id'])) {
            $this->setId($entries['id']);
        }
        if (isset($entries['name'])) {
            $this->setName($entries['name']);
        }
        if (isset($entries['version'])) {
            $this->setVersion($entries['version']);
        }
        $url = [];
        if (isset($entries['url_controller'])) {
            $url['controller'] = $entries['url_controller'];
        }
        if (isset($entries['url_action'])) {
            $url['action'] = $entries['url_action'];
        }
        if ($url) {
            $this->setUrl($url);
        }
        if (isset($entries['url_idkey'])) {
            $this->setUrlIdKey($entries['url_idkey']);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): Modules
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Modules
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version): Modules
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrl(): array
    {
        return $this->url;
    }

    /**
     * @param array $url
     * @return $this
     */
    public function setUrl(array $url): Modules
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlIdKey(): string
    {
        return $this->urlIdKey;
    }

    /**
     * @param string $urlIdKey
     * @return $this
     */
    public function setUrlIdKey(string $urlIdKey): Modules
    {
        $this->urlIdKey = $urlIdKey;

        return $this;
    }

    /**
     * @param string $modul
     * @return $this
     */
    public function makeHasModul(string $modul = ''): Modules
    {
        if (!$modul) {
            $modul = $this->getName();
        }

        $moduleMapper = new ModuleMapper();
        $this->setHasModul((bool)$moduleMapper->getModuleByKey($modul));

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasModul(): bool
    {
        return $this->hasModul;
    }

    /**
     * @param boolean $hasModul
     * @return $this
     */
    public function setHasModul(bool $hasModul): Modules
    {
        $this->hasModul = $hasModul;

        return $this;
    }


    /**
     * @param \Modules\User\Models\User|null $user
     * @param string $modul
     * @return $this
     */
    public function makeHasAccess(?\Modules\User\Models\User $user = null, string $modul = ''): Modules
    {
        if (!$modul) {
            $modul = $this->getName();
        }
        if (!$modul) {
            return $this;
        }

        $accesses = new Accesses(new Request(false));

        $adminAccess = false;
        if ($user) {
            $accesses->setUser($user);

            if ($user->isAdmin()) {
                $adminAccess = true;
            }
        }

        $this->setHasAccess($adminAccess || $accesses->hasAccess('Module', $modul));

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasAccess(): bool
    {
        return $this->hasAccess;
    }

    /**
     * @param boolean $hasAccess
     * @return $this
     */
    public function setHasAccess(bool $hasAccess): Modules
    {
        $this->hasAccess = $hasAccess;

        return $this;
    }

    /**
     * @param string $version
     * @param string $modul
     * @return $this
     */
    public function makeHasVersion(string $version = '', string $modul = ''): Modules
    {
        if (!$version) {
            $this->setHasVersion(true);
            return $this;
        }
        if (!$modul) {
            $modul = $this->getName();
        }

        if (!in_array($modul, ['modules', 'layouts'])) {
            $moduleMapper = new ModuleMapper();
            $modulModel = $moduleMapper->getModuleByKey($modul);
        } else {
            $modulModel = new ModuleModel();
            $modulModel->setKey($modul);
            $modulModel->setOfficial(true);
            $modulModel->setSystemModule(true);
        }

        $this->setHasVersion(version_compare($version, ($modulModel ? ($modulModel->getSystemModule() ? VERSION : $modulModel->getVersion()) : ''), '<='));

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasVersion(): bool
    {
        return $this->hasVersion;
    }

    /**
     * @param boolean $hasVersion
     * @return $this
     */
    public function setHasVersion(bool $hasVersion): Modules
    {
        $this->hasVersion = $hasVersion;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCanExecute(): bool
    {
        return $this->getHasAccess() && $this->getHasVersion() && $this->getHasModul();
    }

    /**
     * @return boolean
     */
    public function getChecked(): bool
    {
        return $this->checked;
    }

    /**
     * @param boolean $checked
     * @return $this
     */
    public function setChecked(bool $checked): Modules
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlRoute(): string
    {
        return $this->urlRoute;
    }

    /**
     * @param string $urlRoute
     * @return $this
     */
    public function setUrlRoute(string $urlRoute): Modules
    {
        $this->urlRoute = $urlRoute;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return $this
     */
    public function setOrderBy(string $orderBy): Modules
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): Modules
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param bool $withId
     * @return array
     */
    public function getArray(bool $withId = true): array
    {
        $url = $this->getUrl();
        return array_merge(
            ($withId ? ['id' => $this->getId()] : []),
            [
                'name'      => $this->getName(),
                'version'   => $this->getVersion(),
                'url_idkey' => $this->getUrlIdKey(),
            ],
            ($url['controller'] ? ['url_controller' => $url['controller']] : []),
            ($url['action'] ? ['url_action' => $url['action']] : [])
        );
    }
}
