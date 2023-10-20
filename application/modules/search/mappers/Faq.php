<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Modules\Search\Models\Search as SearchModel;

class Faq extends \Modules\Faq\Mappers\Faq
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $ModulesModel = $searchMapper->getSearchModules('faq');
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $select = $this->db()->select(['f.id', 'f.cat_id', 'f.question', 'f.answer'])
            ->from(['f' => 'faqs'])
            ->join(['ra' => 'faqs_cats_access'], 'f.cat_id = ra.cat_id', 'LEFT', ['read_access' => 'GROUP_CONCAT(ra.group_id)'])
            ->join(['c' => 'faqs_cats'], 'f.cat_id = c.id', 'LEFT', ['c.title', 'c.read_access_all'])
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'f.text' ? 'f.question' : 'f.id')) : 'f.id') => $order]);


        $select = $searchMapper->getcustomwhere($select, $search, ['f.question', 'f.answer']);
        if ($select === false) {
            return [];
        }

        var_dump($select->generateSql());
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
                $searchModel->setResult($resultrow['question']);
                $searchModel->setReadAccess($resultrow['read_access']);
                if (isset($resultrow['read_access_all'])) {
                    if ($resultrow['read_access_all']) {
                        $searchModel->setReadAccess('all');
                    }
                }
                $searchModel->setRow($resultrow);
                $results[] = $searchModel;
            }
        }

        return $results;
    }
}
