<?php


namespace App\Handler\TerritoryPortrait;

use App\Entity\ImportModel;
use App\Handler\Data\DataMapHandler;
use App\Repository\DataColumnRepository;
use App\Repository\DataRepository;
use App\Repository\ImportModelRepository;
use App\Repository\PlaceRepository;
use App\Services\TerritoryDataPortraitService;
use App\Services\TerritoryMapPortraitService;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class TerritoryPortraitHandler
 * @package App\Handler\TerritoryPortrait
 */
class TerritoryPortraitHandler extends DataMapHandler
{
    /**
     * @var TerritoryDataPortraitService
     */
    protected $territoryDataPortraitService;
    /**
     * @var PlaceRepository
     */
    protected $placeRepository;

    /**
     * DataHandler constructor.
     * @param DataColumnRepository $dataColumnRepository
     * @param DataRepository $dataRepository
     * @param ImportModelRepository $importModelRepository
     * @param PlaceRepository $placeRepository
     * @param TerritoryDataPortraitService $territoryDataPortraitService
     * @param TerritoryMapPortraitService $mapViewService
     */
    public function __construct(
        DataColumnRepository $dataColumnRepository,
        DataRepository $dataRepository,
        ImportModelRepository $importModelRepository,
        PlaceRepository $placeRepository,
        TerritoryDataPortraitService $territoryDataPortraitService,
        TerritoryMapPortraitService $mapViewService
    ) {
        parent::__construct($dataColumnRepository, $dataRepository, $importModelRepository, $mapViewService);
        $this->placeRepository = $placeRepository;
        $this->territoryDataPortraitService = $territoryDataPortraitService;
    }

    /**
     * @param ArrayCollection $importModels
     * @param array $data
     * @param array $filters
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function mergeFinancialData(ArrayCollection $importModels, array &$data, array $filters)
    {
        $filters['isFinancial'] = true;

        /** @var ImportModel $importModel */
        foreach ($importModels as $importModel) {
            if (!$importModel->isMapView()) {
                continue;
            }

            $filters['importModels'] = [$importModel->getId()];

            $financialMapData = $this->mapViewService->getFinancialMapData($filters);
            $financialTabData = $this->territoryDataPortraitService->createCrosstabView($importModel, $filters);
            $centroid = $this->placeRepository->getCentroidPlace($filters);
            $data['financial'][$importModel->getName()]['map'] = $financialMapData;
            $data['financial'][$importModel->getName()]['map']['perimeter_center']['lat'] = $centroid['lat'];
            $data['financial'][$importModel->getName()]['map']['perimeter_center']['long'] = $centroid['long'];
            $data['financial'][$importModel->getName()]['tab'] = $financialTabData;
        }
    }

    /**
     * @param ArrayCollection $importModels
     * @param array $data
     * @param array $filters
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function mergeIndicatorData(ArrayCollection $importModels, array &$data, array $filters)
    {
        /** @var ImportModel $importModel */
        foreach ($importModels as $importModel) {
            if (!$importModel->isMapView()) {
                continue;
            }

            $filters['importModel'] = $importModel;

            $indicatorMapData = $this->mapViewService->getIndicatorMapData($filters);
            $indicatorTabData = $this->territoryDataPortraitService->createCrosstabView($importModel, $filters);
            $centroid = $this->placeRepository->getCentroidPlace($filters);
            $data['indicator'][$importModel->getName()]['map'] = $indicatorMapData;
            $data['indicator'][$importModel->getName()]['map']['perimeter_center']['lat'] = $centroid['lat'];
            $data['indicator'][$importModel->getName()]['map']['perimeter_center']['long'] = $centroid['long'];
            $data['indicator'][$importModel->getName()]['map']['perimeter_zoom'] = 10;
            $data['indicator'][$importModel->getName()]['tab'] = $indicatorTabData;
        }
    }
}
