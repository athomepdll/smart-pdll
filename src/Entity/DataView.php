<?php

namespace App\Entity;

/**
 * Class DataView
 * @package App\Entity
 */
class DataView extends Entity
{
    protected $importModelName;
    protected $importModel;
    protected $dataLine;
    protected $city;
    protected $sirenCarrier;
    protected $columnName;
    protected $value;
    protected $cityName;
    protected $dataLevel;
    protected $year;
    protected $importType;
    protected $department;
    protected $district;
    protected $hasCityChanged;

    private function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getImportModel()
    {
        return $this->importModel;
    }

    /**
     * @return mixed
     */
    public function getDataLine()
    {
        return $this->dataLine;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getSirenCarrier()
    {
        return $this->sirenCarrier;
    }

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * @return mixed
     */
    public function getDataLevel()
    {
        return $this->dataLevel;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return mixed
     */
    public function getImportType()
    {
        return $this->importType;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @return mixed
     */
    public function getHasCityChanged()
    {
        return $this->hasCityChanged;
    }
}
