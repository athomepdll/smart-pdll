<?php


namespace App\Services\Place;

/**
 * Class DistrictImportService
 * @package App\Services\Place
 */
class DistrictsImportService extends AbstractPlaceImportService
{
    /**
     * @param $properties
     * @return string
     */
    protected function getCode($properties)
    {
        return $properties->INSEE_DEP . $properties->INSEE_ARR;
    }
}
