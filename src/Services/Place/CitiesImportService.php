<?php


namespace App\Services\Place;

/**
 * Class CitiesImportService
 * @package App\Services\Place
 */
class CitiesImportService extends AbstractPlaceImportService
{

    /**
     * @param $properties
     * @return string
     */
    protected function getCode($properties)
    {
        return $properties->INSEE_COM;
    }
}
