<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Modules\Search\Models\Search as SearchModel;

class Downloads extends \Modules\Downloads\Mappers\Downloads
{
    /**
     * @param Search $searchMapper
     * @param string $order
     * @return SearchModel[]|null
     */
    public function getSearch(Search $searchMapper, string $order = 'DESC'): ?array
    {
        $ModulesModel = $searchMapper->getSearchModules('downloads');
        $search = $searchMapper->getSearcharray();

        if (empty($search)) {
            return [];
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            return [];
        }

        $select = $this->db()->select(['f.file_id', 'f.cat', 'fileid' => 'f.id', 'f.visits', 'f.file_title', 'f.file_description', 'f.file_image'])
            ->from(['f' => 'downloads_files'])
            ->join(['m' => 'media'], 'm.id = f.file_id', 'LEFT', ['m.url', 'm.id', 'm.url_thumb'])
            ->join(['c' => 'downloads_items'], 'c.id = f.cat', 'LEFT', [])
            ->order([($ModulesModel->getOrderBy() ? (($ModulesModel->getOrderBy() == 'text' ? 'f.file_title' : 'f.id')) : 'f.id') => $order]);

        $select = $searchMapper->getcustomwhere($select, $search, ['f.file_title', 'f.file_description']);
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
            if ($resultrow['fileid']) {
                $searchModel = new SearchModel();
                $searchModel->setId($resultrow['fileid']);
                $searchModel->setResult($resultrow['file_title']);
                $searchModel->setRow($resultrow);
                $results[] = $searchModel;
            }
        }

        return $results;
    }
}
