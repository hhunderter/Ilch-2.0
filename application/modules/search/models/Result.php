<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Models;

class Result extends \Ilch\Model
{
    /**
     * @var string
     */
    protected $uid = '';

    /**
     * @var string
     */
    protected $search = '';

    /**
     * @var string
     */
    protected $module = '';

    /**
     * @var string
     */
    protected $days = 0;

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var String
     */
    protected $dateCreated = '';

    /**
     * @param array $entries
     * @return $this
     */
    public function setByArray(array $entries): Result
    {
        if (isset($entries['uid'])) {
            $this->setUid($entries['uid']);
        }
        if (isset($entries['search'])) {
            $this->setSearch($entries['search']);
        }
        if (isset($entries['module'])) {
            $this->setModule($entries['module']);
        }
        if (isset($entries['days'])) {
            $this->setDays($entries['days']);
        }
        if (isset($entries['result'])) {
            $this->setResult($entries['result']);
        }
        if (isset($entries['dateCreated'])) {
            $this->setDateCreated($entries['dateCreated']);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return $this
     */
    public function setUid(string $uid): Result
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function setSearch(string $search): Result
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     * @return $this
     */
    public function setModule(string $module): Result
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return string
     */
    public function getDays(): string
    {
        return $this->days;
    }

    /**
     * @param string $days
     * @return $this
     */
    public function setDays(string $days): Result
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     * @return $this
     */
    public function setResult(string $result): Result
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return String
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @param String $dateCreated
     * @return $this
     */
    public function setDateCreated(string $dateCreated): Result
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Gets the Array of Model.
     *
     * @return array
     */
    public function getArray(): array
    {
        return [
            'uid'           => $this->getUid(),
            'result'        => $this->getResult(),
            'search'        => $this->getSearch(),
            'module'        => $this->getModule(),
            'days'          => $this->getDays(),
            'dateCreated'   => $this->getDateCreated(),
        ];
    }
}
