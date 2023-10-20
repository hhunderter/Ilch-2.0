<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Boxes;

use Ilch\Registry;
use Modules\Search\Mappers\Search as SearchMapper;

class Search extends \Ilch\Box
{
    public function render()
    {
        $searchMapper = new SearchMapper($this->getView(), $this->getUser());
        $SearchModules = $searchMapper->getSearchModules();

        $config = Registry::get('config');

        $modules = $config->get('search_boxmodule');
        if ($modules) {
            $modules = explode(",", $modules);
            foreach ($SearchModules as $key => $searchModel) {
                if (in_array($key, $modules) && $searchModel->getHasModul()) {
                    $searchMapper->modifySearchModules($key, true);
                }
            }
        } else {
            $searchMapper->modifySearchModules('forum', true);
        }

        $searchMapper->setHTMLOption(false);
        $searchMapper->setHTMLOption(false, 'button');

        $this->getView()->set('searchMapper', $searchMapper);
    }
}
