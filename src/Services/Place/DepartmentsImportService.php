<?php


namespace App\Services\Place;

/**
 * Class DepartmentsImportService
 * @package App\Services\Place
 */
class DepartmentsImportService extends AbstractPlaceImportService
{
    /**
     * @param $properties
     * @return string
     */
    protected function getCode($properties)
    {
        return $properties->INSEE_DEP;
    }
}
