<?php


namespace App\Entity\Columns;

use App\Entity\Data;
use App\Entity\DataLevel;
use App\Entity\DataType;
use AthomeSolution\ImportBundle\Entity\Column as ImportBundleColumn;

/**
 * Class Column
 * @package App\Entity\Columns
 */
class DataColumn extends ImportBundleColumn
{
    /**
     * @var DataType
     */
    protected $dataType;

    /**
     * @var DataLevel
     */
    protected $dataLevel;

    public function __construct()
    {
        $this->class = Data::class;
        $this->method = 'setValue';
    }

    /**
     * @return DataType|null
     */
    public function getDataType(): ?DataType
    {
        return $this->dataType;
    }

    /**
     * @param DataType|null $dataType
     * @return DataColumn
     */
    public function setDataType(?DataType $dataType)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * @return DataLevel
     */
    public function getDataLevel(): ?DataLevel
    {
        return $this->dataLevel;
    }

    /**
     * @param DataLevel $dataLevel
     */
    public function setDataLevel(?DataLevel $dataLevel): void
    {
        $this->dataLevel = $dataLevel;
    }
}
