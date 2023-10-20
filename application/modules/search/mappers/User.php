<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Modules\Search\Models\Search as SearchModel;

class User extends \Modules\User\Mappers\User
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $ModulesModel = $searchMapper->getSearchModules('user');
        $search = $searchMapper->getSearcharray();
        $option = $ModulesModel->getOptions();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $select = $this->db()->select('*')
            ->from('users')
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'text' ? 'name' : 'date_created')) : 'id') => $order]);

        $select = $searchMapper->getcustomwhere($select, $search, $option['keyname'], 'date_created', 'id');
        if ($select === false) {
            return [];
        }

        //die(var_dump($select->generateSql()));
        $result = $select->execute();
        $resultArray = $result->fetchRows();

        if (empty($resultArray)) {
            return null;
        }

        $results = [];

        foreach ($resultArray as $resultrow) {
            if ($resultrow['id']) {
                $searchModel = new SearchModel();
                $searchModel->setId($resultrow['id']);
                $searchModel->setResult($resultrow['name']);
                $searchModel->setDateCreated($resultrow['date_created']);
                $searchModel->setRow($resultrow);
                $results[] = $searchModel;
            }
        }

        return $results;
    }
}
