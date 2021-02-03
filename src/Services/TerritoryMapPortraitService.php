<?php


namespace App\Services;

use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TerritoryPortraitService
 * @package App\Services
 */
class TerritoryMapPortraitService extends MapViewService
{


    /**
     * MapViewService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @param float $max
     * @param float $min
     * @param float $median
     * @return array
     */
    protected function getCleanedDataHeader(float $max, float $min, float $median)
    {
        return [
            'max' => $max,
            'min' => $min,
            'median' => $median,
            'perimeter_zoom' => 10,
            'legend' => [
                'sizeMax' => self::SIZES['max'],
                'sizeMedian' => self::SIZES['median'],
                'sizeMin' => self::SIZES['min'],
                'import_models' => []
            ],
            'cities' => []
        ];
    }
}
