<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Models;

class Search extends \Ilch\Model
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var String
     */
    protected $dateCreated = '';

    /**
     * @var string
     */
    protected $readAccess = '';

    /**
     * @var array
     */
    protected $row = [];


    /**
     * @param array $entries
     * @return $this
     */
    public function setByArray(array $entries): Search
    {
        if (isset($entries['id'])) {
            $this->setId($entries['id']);
        }
        if (isset($entries['result'])) {
            $this->setResult($entries['result']);
        }
        if (isset($entries['dateCreated'])) {
            $this->setDateCreated($entries['dateCreated']);
        }
        if (isset($entries['readAccess'])) {
            $this->setReadAccess($entries['readAccess']);
        }
        if (isset($entries['row'])) {
            $this->setRow($entries['row']);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): Search
    {
        $this->id = $id;

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
    public function setResult(string $result): Search
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
    public function setDateCreated(string $dateCreated): Search
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getReadAccess(): string
    {
        return $this->readAccess;
    }

    /**
     * @param string $readAccess
     * @return $this
     */
    public function setReadAccess(string $readAccess): Search
    {
        $this->readAccess = $readAccess;

        return $this;
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * @param array $row
     * @return $this
     */
    public function setRow(array $row): Search
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Gets the Array of Model.
     *
     * @param bool $withId
     * @return array
     */
    public function getArray(bool $withId = true): array
    {
        return array_merge(
            ($withId ? ['id' => $this->getId()] : []),
            [
                'result'        => $this->getResult(),
                'dateCreated'   => $this->getDateCreated(),
                'readAccess'    => $this->getReadAccess(),
                'row'           => $this->getRow(),
            ]
        );
    }
}
