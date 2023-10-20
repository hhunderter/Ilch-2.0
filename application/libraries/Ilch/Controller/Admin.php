<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Ilch\Controller;

use Modules\Admin\Mappers\Module as ModuleMapper;
use Modules\Search\Mappers\Search as SearchMapper;

class Admin extends Base
{
    public function __construct(\Ilch\Layout\Base $layout, \Ilch\View $view, \Ilch\Request $request, \Ilch\Router $router, \Ilch\Translator $translator)
    {
        $searchMapper = new SearchMapper($view, $this->getUser(), true);
        $searchMapper->modifySearchModules('all', true);
        $searchMapper->setHTMLOption(false, 'all');

        $moduleMapper = new ModuleMapper();
        parent::__construct($layout, $view, $request, $router, $translator);

        $this->getLayout()->set('menu', []);
        $this->getLayout()->setFile('modules/admin/layouts/index');
        $this->getLayout()->set('modules', $moduleMapper->getModules());
        $this->getLayout()->set('accesses', $this->getAccesses());
        $this->getLayout()->set('searchMapper', $searchMapper);
    }
}
