<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Mappers;

use Ilch\Registry;
use Ilch\View;
use Modules\Search\Models\Modules as ModulesModel;
use Modules\Search\Models\Search as SearchModel;
use Modules\Search\Models\Result as ResultModel;
use Modules\User\Models\User;

class Search extends \Ilch\Mapper
{
    /**
     * @var string
     */
    public $tableName = 'search';
    /**
     * @var string
     */
    public $tableNameResult = 'search_result';

    /**
     * @var bool|null
     */
    public $tableExist = null;

    /**
     * @var array
     */
    private $searchDays = [
        '365'   => ['text' => 'all365', 'onlyAdmin' => false],
        '1'     => ['text' => '1day', 'onlyAdmin' => false],
        '7'     => ['text' => '7days', 'onlyAdmin' => false],
        '14'    => ['text' => '2weeks', 'onlyAdmin' => false],
        '30'    => ['text' => '1month', 'onlyAdmin' => false],
        '90'    => ['text' => '3months', 'onlyAdmin' => false],
        '180'   => ['text' => '6months', 'onlyAdmin' => false],
        null    => ['text' => 'all', 'onlyAdmin' => true],
    ];

    /**
     * @var null|View
     */
    private $view = null;

    /**
     * @var null|User
     */
    private $user = null;

    /**
     * @var null|string
     */
    private $days = null;

    /**
     * @var array
     */
    private $SearchModulesArray = [];

    /**
     * @var boolean
     */
    private $searchText = "";

    /**
     * @var array
     */
    private $searchArray = [];

    /**
     * @var boolean
     */
    private $show_tooltip = true;

    /**
     * @var boolean
     */
    private $show_options = true;

    /**
     * @var boolean
     */
    private $show_days = true;

    /**
     * @var boolean
     */
    private $show_button = true;

    /**
     * @var array
     */
    private $results = [];

    /**
     * @param View $view
     * @param User|null $user
     * @param bool $isAdminPage
     */
    public function __construct(View $view, ?User $user = null, bool $isAdminPage = false)
    {
        parent::__construct();

        if (is_a($user, User::class)) {
            $this->setUser($user);
        }
        if (is_a($view, View::class)) {
            $this->setView($view);
        }

        $this->checkDays();

        $modulesModel = new ModulesModel();
        $modulesModel->setName('user')
            ->setHasModul(true)
            ->makeHasAccess($this->getUser())
            ->makeHasVersion('2.1.53')
            ->setUrl(['controller' => 'profil', 'action' => 'index'])
            ->setUrlIdKey('user')
            ->setOptions(['keyname' => ['name']]);
        $this->setSearchModules($modulesModel, '');

        $modulesModel = new ModulesModel();
        $modulesModel->setName('article')
            ->setHasModul(true)
            ->makeHasAccess($this->getUser())
            ->makeHasVersion('2.1.53')
            ->setUrl(['controller' => 'index', 'action' => 'show']);
        $this->setSearchModules($modulesModel, '');

        $modulesModels = $this->getEntries() ?? [];

        foreach ($modulesModels as $modulesModel) {
            $modulesModel->makeHasModul()
                ->makeHasAccess($this->getUser())
                ->makeHasVersion($modulesModel->getVersion());
            $this->setSearchModules($modulesModel, '');
        }

        if ($isAdminPage && $this->getUser()->isAdmin()) {
            $modulesModel = new ModulesModel();
            $modulesModel->setName('modules')
                ->setHasModul(true)
                ->setHasAccess(true)
                ->makeHasVersion('2.1.53')
                ->setUrl(['module' => 'admin', 'controller' => 'modules', 'action' => 'show'])
                ->setUrlroute('admin');
            $this->setSearchModules($modulesModel, '');

            $modulesModel = new ModulesModel();
            $modulesModel->setName('layouts')
                ->setHasModul(true)
                ->setHasAccess(true)
                ->makeHasVersion('2.1.53')
                ->setUrl(['module' => 'admin', 'controller' => 'layouts', 'action' => 'show'])
                ->setUrlroute('admin');
            $this->setSearchModules($modulesModel, '');
        }

        $modulesModel = new ModulesModel();
        $modulesModel->setName('all');
        $this->setSearchModules($modulesModel, '');
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): Search
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return View|null
     */
    public function getView(): ?View
    {
        return $this->view;
    }

    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view): Search
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param bool $force
     * @return bool
     */
    public function checkDB(bool $force = false): bool
    {
        if ($this->tableExist === null || $force) {
            $this->tableExist = $this->db()->ifTableExists($this->tableName) && $this->db()->ifTableExists($this->tableNameResult);
        }
        return $this->tableExist;
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @param \Ilch\Pagination|null $pagination
     * @return ModulesModel[]|null
     */
    public function getEntriesBy(array $where = [], array $orderBy = ['id' => 'DESC'], ?\Ilch\Pagination $pagination = null): ?array
    {
        if (!$this->checkDB()) {
            return null;
        }

        $select = $this->db()->select()
            ->fields(['*'])
            ->from([$this->tableName])
            ->where($where)
            ->order($orderBy);

        if ($pagination !== null) {
            $select->limit($pagination->getLimit())
                ->useFoundRows();
            $result = $select->execute();
            $pagination->setRows($result->getFoundRows());
        } else {
            $result = $select->execute();
        }

        $entriesArray = $result->fetchRows();
        if (empty($entriesArray)) {
            return null;
        }
        $entries = [];

        foreach ($entriesArray as $entry) {
            $entryModel = new ModulesModel();

            $entryModel->setByArray($entry);

            $entries[] = $entryModel;
        }
        return $entries;
    }

    /**
     * @param array $where
     * @return ModulesModel[]|null
     */
    public function getEntries(array $where = []): ?array
    {
        return $this->getEntriesBy($where, []);
    }

    /**
     * @param int|ModulesModel $id
     * @return null|ModulesModel
     */
    public function getEntryById(int $id): ?ModulesModel
    {
        if (is_a($id, ModulesModel::class)) {
            $id = $id->getId();
        }

        $entries = $this->getEntriesBy(['id' => (int)$id], []);

        if (!empty($entries)) {
            return reset($entries);
        }

        return null;
    }

    /**
     * @param ModulesModel $model
     * @return int
     */
    public function save(ModulesModel $model): int
    {
        if (!$this->checkDB()) {
            return 0;
        }

        $fields = $model->getArray();

        if ($model->getId()) {
            $this->db()->update($this->tableName)
                ->values($fields)
                ->where(['id' => $model->getId()])
                ->execute();
            $result = $model->getId();
        } else {
            $result = $this->db()->insert($this->tableName)
                ->values($fields)
                ->execute();
        }

        return $result;
    }

    /**
     * @param ModulesModel|int $id
     * @return boolean
     */
    public function delete($id): bool
    {
        if (!$this->checkDB()) {
            return false;
        }

        if (is_a($id, ModulesModel::class)) {
            $id = $id->getId();
        }

        return $this->db()->delete($this->tableName)
            ->where(['id' => (int)$id])
            ->execute();
    }

    /**
     * @param string|null $key
     * @return array|null
     */
    public function getSearchDays(?string $key = null): ?array
    {
        if (is_string($key)) {
            return $this->searchDays[$key] ?? null;
        } else {
            return $this->searchDays;
        }
    }

    /**
     * @param string|null $days
     * @return null|string
     */
    public function checkDays(?string $days = null): ?string
    {
        if ($days === null) {
            $this->setDays(key($this->getSearchDays()));
        } else {
            if (!empty($days) && is_numeric($days)) {
                if (!$this->getSearchDays($days)) {
                    $days = key($this->getSearchDays());
                }
                $this->setDays($days);
            } else {
                $this->setDays(null);
            }
        }
        return $this->getDays();
    }

    /**
     * @return null|string
     */
    public function getDays(): ?string
    {
        return $this->days;
    }

    /**
     * @param null|string $days
     * @return $this
     */
    public function setDays(?string $days): Search
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @param ModulesModel|array $searchModulesArray
     * @param string|null $modulKey
     * @return $this
     */
    public function setSearchModules($searchModulesArray, string $modulKey = null): Search
    {
        if ($modulKey == '') {
            $modulKey = $searchModulesArray->getName();
        }

        if ($modulKey != null) {
            $this->SearchModulesArray[$modulKey] = $searchModulesArray;
        } else {
            $this->SearchModulesArray = $searchModulesArray;
        }

        return $this;
    }

    /**
     * @param string|null $modulKey
     * @param boolean|string $value
     * @param string $option
     * @return boolean|$this
     */
    public function modifySearchModules(?string $modulKey, $value, string $option = "setChecked")
    {
        if ($modulKey !== null && isset($this->SearchModulesArray[$modulKey])) {
            $this->SearchModulesArray[$modulKey]->$option($value);
        } else {
            return false;
        }

        return $this;
    }

    /**
     * @param string|null $key
     * @return array|ModulesModel
     */
    public function getSearchModules(?string $key = null)
    {
        if (is_string($key)) {
            return $this->SearchModulesArray[$key] ?? new ModulesModel();
        } else {
            return $this->SearchModulesArray;
        }
    }

    /**
     * @return string
     */
    public function getSearchText(): string
    {
        return $this->searchText;
    }

    /**
     * @param string $searchText
     * @return $this
     */
    public function setSearchText(string $searchText): Search
    {
        $this->searchText = $searchText;

        return $this;
    }

    /**
     * @param string $searchText
     * @param string|null $dateCreated
     * @return array
     */
    public function makeSearcharray(string $searchText = "", ?string $dateCreated = null): array
    {
        $searchText = str_replace(['%', ';'], '', strtolower(trim($searchText)));
        $searchText = str_replace('\'', '"', $searchText);

        $this->setSearchText($searchText);

        /** @var \Ilch\Database\Mysql $db */
        $db = Registry::get('db');
        $searchArray = [];

        if (!empty($dateCreated) && is_numeric($dateCreated)) {
            $date = new \Ilch\Date((new \Ilch\Date())->format($db::FORMAT_DATE));
            $date->modify('-' . $dateCreated . ' Days');
            $searchArray['>=']['date_created'] = $date->format($db::FORMAT_DATETIME);
        }

        preg_match_all('/user:\"(.+)\"/U', $this->getSearchText(), $trefferUser);
        $search = preg_replace(['/user:\"(.+)\"/U', '/user:\"\"/U'], '', $this->getSearchText());

        if (count($trefferUser[1]) > 0) {
            $userMapper = new \Modules\User\Mappers\User();
            if (is_numeric($trefferUser[1][0])) {
                $searchArray['=']['name'] = $trefferUser[1][0];
            } else {
                $searchUser = $userMapper->getUserByName($trefferUser[1][0]);
                if ($searchUser) {
                    $searchArray['=']['name'] = $searchUser->getId();
                }
            }
        }

        //find exact search
        preg_match_all('/\"(.+)\"/U', $search, $treffer);
        $search = preg_replace('/\"(.+)\"/U', '', $search);
        $search = preg_replace('/\"\"/U', '', $search);

        foreach (explode(" ", $search) as $searchExplode) {
            $searchExplode = trim($searchExplode);
            if (!empty($searchExplode)) {
                if (substr($searchExplode, 0, 1) == "+") {
                    if (((isset($searchArray['LIKE']) && !in_array(substr($searchExplode, 1), $searchArray['LIKE'])) || !isset($searchArray['LIKE'])) && ((isset($searchArray['NOT LIKE']) && !in_array(substr($searchExplode, 1), $searchArray['NOT LIKE'])) || !isset($searchArray['NOT LIKE']))) {
                        $searchArray['LIKE'][] = substr($searchExplode, 1);
                    }
                } elseif (substr($searchExplode, 0, 1) == "-") {
                    if (((isset($searchArray['LIKE']) && !in_array(substr($searchExplode, 1), $searchArray['LIKE'])) || !isset($searchArray['LIKE'])) && ((isset($searchArray['NOT LIKE']) && !in_array(substr($searchExplode, 1), $searchArray['NOT LIKE'])) || !isset($searchArray['NOT LIKE']))) {
                        $searchArray['NOT LIKE'][] = substr($searchExplode, 1);
                    }
                } else {
                    if (((isset($searchArray['LIKE']) && !in_array($searchExplode, $searchArray['LIKE'])) || !isset($searchArray['LIKE'])) && ((isset($searchArray['NOT LIKE']) && !in_array($searchExplode, $searchArray['NOT LIKE'])) || !isset($searchArray['NOT LIKE']))) {
                        $searchArray['LIKE'][] = $searchExplode;
                    }
                }
            }
        }
        foreach ($treffer[1] as $searchExplode) {
            $searchExplode = str_replace(['+', '-'], '', trim($searchExplode));
            if (!empty($searchExplode)) {
                if (((isset($searchArray['LIKE']) && !in_array($searchExplode, $searchArray['LIKE'])) || !isset($searchArray['LIKE'])) && ((isset($searchArray['NOT LIKE']) && !in_array($searchExplode, $searchArray['NOT LIKE'])) || !isset($searchArray['NOT LIKE']))) {
                    $searchArray['LIKE'][] = ' ' . $searchExplode . ' ';
                    $searchArray['LIKE'][] = $searchExplode . ' ';
                    $searchArray['LIKE'][] = ' ' . $searchExplode;
                    if (($this->getUser() && $this->getUser()->isAdmin())) {
                        $searchArray['LIKE'][] = $searchExplode;
                    }
                }
            }
        }

        $this->setSearcharray($searchArray);
        return $searchArray;
    }

    /**
     * @param array $searchArray
     * @param string|null $key
     * @return $this
     */
    public function setSearcharray(array $searchArray, ?string $key = null): Search
    {
        if ($key !== null) {
            $this->searchArray[$key] = $searchArray;
        } else {
            $this->searchArray = $searchArray;
        }

        return $this;
    }

    /**
     * @param string|null $key
     * @return array
     */
    public function getSearcharray(?string $key = null): array
    {
        if ($key !== null) {
            return $this->searchArray[$key];
        } else {
            return $this->searchArray;
        }
    }

    /**
     * @param boolean $value
     * @param string $option
     * @return $this
     */
    public function setHTMLOption(bool $value = true, string $option = 'all'): Search
    {
        switch ($option) {
            case "all":
            default:
                $this->show_tooltip = $value;
                $this->show_options = $value;
                $this->show_days = $value;
                break;
            case "tooltip":
                $this->show_tooltip = $value;
                break;
            case "module":
                $this->show_options = $value;
                break;
            case "days":
                $this->show_days = $value;
                break;
            case "button":
                $this->show_button = $value;
                break;
        }
        return $this;
    }

    /**
     * @param bool $print
     * @return String
     */
    public function getInputHTML(bool $print = false): string
    {
        $return = '<div id="search-options-div">';
        if (!$this->show_options) {
            foreach ($this->getSearchModules() as $key => $modulesModel) {
                if (($modulesModel->getChecked() && $modulesModel->getCanExecute()) || ($key == 'all' && $modulesModel->getChecked())) {
                    $return .= '<input type="hidden" name="search_options[]" value="' . $key . '">';
                }
            }
        }
        if (!$this->show_days) {
            $return .= '<input type="hidden" name="days" value="' . $this->getDays() . '">';
        }
        $return .= '<div class="input-group">
                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="' . $this->getView()->getTrans('search') . '" value=\'' . $this->getView()->originalInput('search_text', $this->getSearchText()) . '\'>';
        if ($this->show_button) {
            $return .= '<div class="input-group-btn">
                    <button type="submit" class="btn btn-default">' . $this->getView()->getTrans('go') . '</button>
                </div>';
        }
        if ($this->show_tooltip) {
            $return .= '<div class="input-group-btn">
                    <a class="btn btn-default" href="#" role="button" data-toggle="modal" data-target="#searchtooltip"><i class="fa-solid fa-circle-question"></i>&nbsp;</a>
                </div>';
        }
        $return .= '</div>';
        if ($this->show_options || $this->show_days) {
            $return .= '<div id="options-div">';
            if ($this->show_options) {
                $return .= '<select id="search_options" name="search_options[]" multiple="multiple">';
                foreach ($this->getSearchModules() as $key => $modulesModel) {
                    if ($modulesModel->getCanExecute()) {
                        $return .= '<option value="' . $key . '" ' . (in_array($key, $this->getView()->originalInput('search_options', [])) || $modulesModel->getChecked() ? 'selected="selected"' : '') . '>' . $this->getView()->getTrans($key) . '</option>';
                    }
                }
                $return .= '</select>';
            }
            if ($this->show_days) {
                if ($this->show_options) {
                    $return .= '<select id="search_days" name="search_days">';
                }
                foreach ($this->getSearchDays() as $day => $dayArray) {
                    if ($day != null || ($dayArray['onlyAdmin'] && ($this->getUser() && $this->getUser()->isAdmin()))) {
                        $return .= '<option value="' . $day . '" ' . ($this->getView()->originalInput('search_days', $this->getDays()) == $day ? 'selected="selected"' : '') . '>' . $this->getView()->getTrans($dayArray['text']) . '</option>';
                    }
                }
                $return .= '</select>';
            }
            $return .= '</div>';
        }
        $return .= '</div>';
        if ($this->show_tooltip) {
            $return .= '<div class="modal fade" id="searchtooltip" tabindex="-1" role="dialog" aria-labelledby="LogMessage">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">' . $this->getView()->getTrans('help') . '</h4>
                            </div>
                            <div class="modal-body">
                                ' . $this->getView()->getTrans('helptext') . '
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">' . $this->getView()->getTrans('close') . '</button>
                            </div>
                        </div>
                    </div>
                </div>';
        }
        if ($this->show_options || $this->show_days || $this->show_tooltip) {
            $return .= '<script>
        $(document).ready(function(){
        ';
            if ($this->show_days) {
                $return .= '$(\'#search_days\').multiselect();
                ';
            }
            if ($this->show_options) {
                $return .= '$(\'#search_options\').multiselect({
                    includeSelectAllOption: true,
                    numberDisplayed: 3,
                    allSelectedText: \'' . $this->getView()->getTrans('selectmodules_all') . '\',
                    nSelectedText: \'' . $this->getView()->getTrans('selectmodules_n') . '\',
                    nonSelectedText: \'' . $this->getView()->getTrans('selectmodules') . '\'
                });
                ';
            }
            if ($this->show_tooltip) {
                $return .= '$(\'#searchtooltip\').tooltip();
                ';
            }
            $return .= '});
        </script>';
        }

        if ($print) {
            echo $return;
        }
        return $return;
    }

    /**
     * @param string $searchText
     * @param array|null $options
     * @return array
     */
    public function makeSearch(string $searchText, ?array $options = null): array
    {
        $results = [];

        $readAccess = [3];
        $adminAccess = '';
        if ($this->getUser()) {
            $readAccess = [];
            foreach ($this->getUser()->getGroups() as $groups) {
                $readAccess[] = $groups->getId();
            }

            if ($this->getUser()->isAdmin()) {
                $adminAccess = true;
            }
        }

        if (!$options) {
            $options = [];
        }


        $this->makeSearcharray($searchText, $this->getDays());

        foreach ($this->getSearchModules() as $key => $modulesModel) {
            if ((in_array('all', $options) || in_array($key, $options)) && $key != 'all') {
                if ($modulesModel->getCanExecute()) {
                    $modulesModel->setChecked(true);

                    $this->setSearchModules($modulesModel, $key);

                    if (!empty($searchText)) {
                        $searchClass = '\\Modules\\Search\\Mappers\\' . ucfirst($key);
                        $searchMappersMapper = new $searchClass();
                        /** @var SearchModel[]|null $searchResults */
                        $searchResults = $searchMappersMapper->getSearch($this);
                        if ($searchResults) {
                            $results[$key] = [];
                            foreach ($searchResults as $searchResultsModel) {
                                if (!empty($searchResultsModel->getReadAccess()) && !is_in_array(array_merge($readAccess, $searchResultsModel->getReadAccess() == 'all' ? ['all'] : []), explode(',', $searchResultsModel->getReadAccess())) && !$adminAccess) {
                                    continue;
                                }
                                $results[$key][] = $searchResultsModel;
                            }
                        }
                    }
                }
            }
        }

        $this->setResults($results);

        return $results;
    }

    /**
     * @return SearchModel[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param SearchModel[] $results
     * @return $this
     */
    public function setResults(array $results): Search
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param \Ilch\Database\Mysql\Select $select
     * @param array|null $search
     * @param array|null $likeOperator
     * @param boolean|string $dateCreatedOperator
     * @param boolean|string $nameOperator
     * @return  boolean|\Ilch\Database\Mysql\Select
     */
    public function getCustomWhere(\Ilch\Database\Mysql\Select $select, ?array $search = null, ?array $likeOperator = [], $dateCreatedOperator = true, $nameOperator = false)
    {
        if (empty($likeOperator)) {
            return false;
        }

        if ($search === null) {
            $search = $this->getSearcharray();
        }

        $db = Registry::get('db');

        $likes = [];
        $orSelect = [];
        foreach ($search as $operator => $keys) {
            $orSelect = [];
            foreach ($keys as $key => $keyName) {
                if (strtolower(substr($operator, -4, 4)) == 'like') {
                    foreach ($likeOperator as $operatorKey) {
                        $likes[$operatorKey . ' ' . $operator] = '%' . $db->escape($keyName) . '%';
                    }
                    $orSelect[] = $select->orX($likes);
                } else {
                    if ($key == 'date_created') {
                        if (is_bool($dateCreatedOperator)) {
                            if (!$dateCreatedOperator) {
                                return false;
                            }
                        } else {
                            $orSelect[] = $select->orX([$dateCreatedOperator . ' ' . $operator => $db->escape($keyName)]);
                        }
                    } elseif ($key == 'name') {
                        if (is_bool($nameOperator)) {
                            if (!$nameOperator) {
                                return false;
                            }
                        } else {
                            $orSelect[] = $select->orX([$nameOperator  . ' ' . $operator => $db->escape($keyName)]);
                        }
                    } elseif (!is_numeric($key)) {
                        $orSelect[] = $select->orX([$key . ' ' . $operator => $db->escape($keyName)]);
                    }
                }
            }
            if (count($search) > 1 && count($orSelect) > 0) {
                $select->andwhere([$select->orX($orSelect)]);
            }
        }
        if (count($search) <= 1) {
            $select->where([$select->orX($orSelect)]);
        }

        return $select;
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @param \Ilch\Pagination|null $pagination
     * @return ResultModel[]|null
     */
    public function getResultsEntriesBy(array $where = [], array $orderBy = ['dateCreated' => 'DESC'], ?\Ilch\Pagination $pagination = null): ?array
    {
        if (!$this->checkDB()) {
            return null;
        }

        $select = $this->db()->select()
            ->fields(['*'])
            ->from([$this->tableNameResult])
            ->where($where)
            ->order($orderBy);

        if ($pagination !== null) {
            $select->limit($pagination->getLimit())
                ->useFoundRows();
            $result = $select->execute();
            $pagination->setRows($result->getFoundRows());
        } else {
            $result = $select->execute();
        }

        $entriesArray = $result->fetchRows();
        if (empty($entriesArray)) {
            return null;
        }
        $entries = [];

        foreach ($entriesArray as $entry) {
            $resultModel = new ResultModel();

            $resultModel->setByArray($entry);

            $entries[] = $resultModel;
        }
        return $entries;
    }

    /**
     * @param string $uid
     * @return ResultModel|null
     */
    public function getEntryResultByUId(string $uid): ?ResultModel
    {
        $entries = $this->getResultsEntriesBy(['uid' => $uid], []);

        if (!empty($entries)) {
            return reset($entries);
        }

        return null;
    }

    /**
     * @param string|null $uid
     * @return $this
     */
    public function backResult(?string $uid = null): Search
    {
        $resultModel = $this->getEntryResultByUId($uid);

        if ($resultModel) {
            $this->setSearchText($resultModel->getSearch());
            $result = [];
            foreach (unserialize($resultModel->getResult()) ?? [] as $key => $entryArray) {
                foreach ($entryArray as $id => $entry) {
                    $newEntry = new SearchModel();
                    $result[$key][$id] = $newEntry->setByArray($entry);
                }
            }
            $this->checkDays($resultModel->getDays());
            foreach (explode(',', $resultModel->getModule()) ?? [] as $modulKey) {
                $this->modifySearchModules($modulKey, true);
            }
            $this->setResults($result);
        }

        return $this;
    }

    /**
     * @param string|null $uid
     * @return ResultModel|null
     */
    public function saveResult(?string $uid = null): ?ResultModel
    {
        if (!$this->checkDB()) {
            return null;
        }

        $result = [];
        /** @var SearchModel[] $searchModels */
        foreach ($this->getResults() as $key => $searchModels) {
            $result[$key] = null;
            foreach ($searchModels as $id => $searchModel) {
                $result[$key][$id] = $searchModel->getArray();
            }
        }
        $module = [];
        /** @var ModulesModel $modulesModels */
        foreach ($this->getSearchModules() as $modulesModels) {
            if ($modulesModels->getChecked()) {
                $module[] = $modulesModels->getName();
            }
        }

        if (!$uid) {
            $uid = generateUUID();
        }
        while ($this->getEntryResultByUId($uid)) {
            $uid = generateUUID();
        }

        $resultModel = new ResultModel();
        $resultModel->setUid($uid)
            ->setResult(serialize($result))
            ->setDateCreated('')
            ->setModule(implode(',', $module))
            ->setDays($this->getDays() ?? '')
            ->setSearch($this->getSearchText());

        $this->db()->insert($this->tableNameResult)
            ->values($resultModel->getArray())
            ->execute();

        return $resultModel;
    }

    /**
     * @param ResultModel|string $uid
     * @return boolean
     */
    public function deleteResult($uid): bool
    {
        if (!$this->checkDB()) {
            return false;
        }

        if (is_a($uid, ResultModel::class)) {
            $uid = $uid->getUid();
        }

        return $this->db()->delete($this->tableNameResult)
            ->where(['uid' => $uid])
            ->execute();
    }
}
