<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Modules\Search\Models\Search as SearchModel;

class Forum extends \Modules\Forum\Mappers\Topic
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $ModulesModel = $searchMapper->getSearchModules('forum');
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $select = $this->db()->select()
            ->from(['i' => 'forum_items'])
            ->join(['aa' => 'forum_accesses'], ['i.id = aa.item_id', 'aa.access_type' => 0], 'LEFT', ['read_access' => 'GROUP_CONCAT(DISTINCT aa.group_id)'])
            ->join(['t' => 'forum_topics'], 'i.id = t.forum_id', 'LEFT', ['t.topic_title', 't.type', 't.visits', 't.status', 't.topic_prefix'])
            ->join(['p' => 'forum_posts'], 'i.id = p.forum_id', 'LEFT', ['p.topic_id', 'p.id', 'p.date_created', 'p.user_id'])
            ->group(['i.id'])
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'text' ? 't.topic_title' : 'p.date_created')) : 'p.topic_id') => $order]);

        $select = $searchMapper->getcustomwhere($select, $search, ['p.text', 't.topic_title'], 'p.date_created', 'p.user_id');
        if ($select === false) {
            return [];
        }

        //var_dump($select->generateSql());
        $result = $select->execute();
        $resultArray = $result->fetchRows();

        if (empty($resultArray)) {
            return null;
        }

        $results = [];
        foreach ($resultArray as $resultrow) {
            if ($resultrow['topic_id']) {
                $searchModel = new SearchModel();
                $searchModel->setId($resultrow['topic_id']);
                $searchModel->setResult($resultrow['topic_title']);
                $searchModel->setDateCreated($resultrow['date_created']);
                $searchModel->setReadAccess($resultrow['read_access']);
                $searchModel->setRow($resultrow);
                $results[] = $searchModel;
            }
        }

        return $results;
    }
}
