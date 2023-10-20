<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Ilch\Registry;
use Modules\Search\Models\Search as SearchModel;

class Modules extends \Modules\Admin\Mappers\Module
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return  SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $ModulesModel = $searchMapper->getSearchModules('modules');
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $locale = Registry::get('translator')->getLocale();
        if (!empty($locale)) {
            $search['='] = ['c.locale' => $locale];
        }

        $select = $this->db()->select(['m.key', 'm.system', 'm.layout', 'm.hide_menu', 'm.author', 'm.version', 'm.link', 'm.icon_small'])
            ->from(['m' => 'modules'])
            ->join(['c' => 'modules_content'], 'c.key = m.key', 'LEFT', ['c.locale', 'c.description', 'c.name'])
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'text' ? 'c.name' : 'm.key')) : 'm.key') => $order]);

        $select = $searchMapper->getcustomwhere($select, $search, ['c.name', 'c.description']);
        if ($select === false) {
            return [];
        }

        //var_dump($select->generateSql());
        $result = $select->execute();
        $resultArray = $result->fetchRows();

        if (empty($resultArray)) {
            return [];
        }

        $results = [];
        $resultsmysql = [];

        foreach ($resultArray as $resultrow) {
            $searchModel = new SearchModel();
            $searchModel->setResult($resultrow['name']);
            $searchModel->setRow($resultrow);
            $resultsmysql[$resultrow['key']] = $searchModel;
        }


        $modulesList = url_get_contents(Registry::get('config')->get('updateserver') . 'modules.php');
        $modulesOnUpdateServer = json_decode($modulesList);
        //var_dump($modulesList);

        if (empty($modulesOnUpdateServer)) {
            return [];
        }

        foreach ($modulesOnUpdateServer as $moduleOnUpdateServer) {
            $searchkey = strtolower($moduleOnUpdateServer->key);
            $searchname = strtolower($moduleOnUpdateServer->name);
            $searchdesc = strtolower($moduleOnUpdateServer->desc);

            $result = false;
            foreach ($search as $operator => $keys) {
                if (strtolower(substr($operator, -4, 4)) == 'like') {
                    if ($operator == 'LIKE' && ($this->arrayFind($searchkey, $keys) !== false || $this->arrayFind($searchname, $keys) !== false || $this->arrayFind($searchdesc, $keys) !== false)) {
                        $result = true;
                    } elseif ($operator == 'NOT LIKE' && ($this->arrayFind($searchkey, $keys) === false || $this->arrayFind($searchname, $keys) === false || $this->arrayFind($searchdesc, $keys) === false)) {
                        $result = true;
                    }
                }
            }

            if ($result) {
                if ($moduleOnUpdateServer->id) {
                    $searchModel = new SearchModel();
                    $searchModel->setId($moduleOnUpdateServer->id);
                    $searchModel->setResult($moduleOnUpdateServer->name);
                    $searchModel->setRow((array)$moduleOnUpdateServer);
                    $results[] = $searchModel;
                }
            }
            if (isset($resultsmysql[$moduleOnUpdateServer->key])) {
                unset($resultsmysql[$moduleOnUpdateServer->key]);
            }
        }

        foreach ($resultsmysql as $SearchresultModel) {
            $results[] = $SearchresultModel;
        }

        if (empty($results)) {
            return null;
        }

        return $results;
    }

    protected function arrayFind(string $needle, $haystack = [])
    {
        foreach ($haystack as $key => $value) {
            if (strpos($needle, $value) !== false) {
                return $key;
            }
        }
        return false;
    }
}
