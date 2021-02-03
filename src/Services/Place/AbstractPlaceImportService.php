<?php


namespace App\Services\Place;

use App\Entity\Place;
use App\Manager\PlaceManager;
use App\Repository\PlaceRepository;

/**
 * Class PlaceImportService
 * @package App\Services
 */
abstract class AbstractPlaceImportService
{
    /**
     * @var PlaceManager
     */
    protected $placeManager;
    /**
     * @var PlaceRepository
     */
    protected $placeRepository;

    /**
     * PlaceImportService constructor.
     * @param PlaceManager $placeManager
     * @param PlaceRepository $placeRepository
     */
    public function __construct(
        PlaceManager $placeManager,
        PlaceRepository $placeRepository
    ) {
        $this->placeManager = $placeManager;
        $this->placeRepository = $placeRepository;
    }

    /**
     * @param string $geojsonFilePath
     */
    public function import(string $geojsonFilePath)
    {
        $content = file_get_contents($geojsonFilePath);
        $data = json_decode($content);

        foreach ($data->features as $feature) {
            $place = new Place();
            $properties = $feature->properties;
            $polygons = $this->getFormattedGeometryInfo($feature->geometry->coordinates);
            $place->setPolygons($polygons);
            $place->setCode($this->getCode($properties));
            $this->placeManager->saveEntity($place);
        }
    }

    /**
     * @param array $coordinates
     * @return string
     */
    protected function getFormattedGeometryInfo(array $coordinates)
    {
        $polygons = 'MULTIPOLYGON(';
        foreach ($coordinates as $key => $coordinate) {
            $polygons .= $key === 0 ? '(' : ',(';
            foreach ($coordinate as $key => $composedPolygons) {
                $newArray = [];
                $polygons .= $key === 0 ? '(' : ',(';
                foreach ($composedPolygons as $polygon) {
                    $newArray []= join(' ', $polygon);
                }
                $polygons .= join(',', $newArray);
                $polygons .= ')';
            }
            $polygons .= ')';
        }

        return $polygons . ')';
    }

    /**
     * @param $properties
     * @return string
     */
    protected function getCode($properties)
    {
        return '';
    }
}
