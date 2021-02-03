<?php


namespace App\Services\Place;

/**
 * Class EpcisImportService
 * @package App\Services\Place
 */
class EpcisImportService extends AbstractPlaceImportService
{
    /**
     * @param $properties
     * @return string
     */
    protected function getCode($properties)
    {
        return $properties->CODE_EPCI;
    }
}
