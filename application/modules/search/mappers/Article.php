<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Ilch\Registry;
use Modules\Search\Models\Search as SearchModel;

class Article extends \Modules\Article\Mappers\Article
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $config = Registry::get('config');

        $ModulesModel = $searchMapper->getSearchModules('article');
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $locale = "";
        if ($config->get('multilingual_acp')) {
            $locale = $searchMapper->getView()->getTranslator()->getLocale();
            if ($locale == $config->get('content_language')) {
                $locale = "";
            }
        }
        if (!empty($locale)) {
            $search['='] = ['pc.locale' => $locale];
        }

        $select = $this->db()->select(['p.id', 'p.cat_id', 'p.date_created', 'p.top', 'p.commentsDisabled'])
            ->from(['p' => 'articles'])
            ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', ['pc.article_id', 'pc.author_id', 'pc.visits', 'pc.content', 'pc.description', 'pc.keywords', 'pc.locale', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.img', 'pc.img_source', 'pc.votes'])
            ->join(['u' => 'users'], 'pc.author_id = u.id', 'LEFT', ['u.name'])
            ->group(['p.id', 'p.cat_id', 'p.date_created', 'p.top', 'pc.article_id', 'pc.author_id', 'pc.visits', 'pc.content', 'pc.description', 'pc.keywords', 'pc.locale', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.img', 'pc.img_source', 'pc.votes'])
            ->join(['ra' => 'articles_access'], 'p.id = ra.article_id', 'LEFT', ['read_access' => 'GROUP_CONCAT(ra.group_id)'])
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'text' ? 'pc.title' : 'p.date_created')) : 'p.id') => $order]);


        $select = $searchMapper->getcustomwhere($select, $search, ['pc.title', 'pc.content'], 'p.date_created', 'pc.author_id');
        if ($select === false) {
            return [];
        }

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
                $searchModel->setResult($resultrow['title']);
                $searchModel->setDateCreated($resultrow['date_created']);
                $searchModel->setReadAccess($resultrow['read_access']);
                $searchModel->setRow($resultrow);
                $results[] = $searchModel;
            }
        }

        return $results;
    }
}
