<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Modules\Search\Models\Search as SearchModel;

class Layouts extends \Ilch\Mapper
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $modulesList = url_get_contents(\Ilch\Registry::get('config')->get('updateserver') . 'layouts.php');
        $modulesOnUpdateServer = json_decode($modulesList);
        //var_dump($modulesList, $modulesOnUpdateServer);

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
