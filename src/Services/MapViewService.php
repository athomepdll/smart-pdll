<?php


namespace App\Services;

use App\Factory\Query\DataFinancialMapQueryFactory;
use App\Factory\Query\DataIndicatorMapQueryFactory;
use App\Factory\Query\LocalizedInfoQueryFactory;
use App\Factory\Query\ProjectGrantQueryFactory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MapProcessorService
 * @package App\Services
 */
class MapViewService
{
    const SIZES = [
        'min' => 25,
        'median' => 48.3,
        'max' => 80
    ];

    /** @var DataFinancialMapQueryFactory */
    protected $dataFinancialMapQueryFactory;

    /** @var DataIndicatorMapQueryFactory */
    protected $dataIndicatorMapQueryFactory;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ProjectGrantQueryFactory */
    protected $projectGrantQueryFactory;

    /** @var LocalizedInfoQueryFactory */
    private $localizedInfoQueryFactory;

    /**
     * MapViewService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->dataFinancialMapQueryFactory = new DataFinancialMapQueryFactory();
        $this->dataIndicatorMapQueryFactory = new DataIndicatorMapQueryFactory();
        $this->entityManager = $entityManager;
        $this->projectGrantQueryFactory = new ProjectGrantQueryFactory();
        $this->localizedInfoQueryFactory = new LocalizedInfoQueryFactory();
    }

    /**
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFinancialMapData(array $filters)
    {
        $filters['isFinancial'] = 'financial';

        $maxProjectGrant = $this->getMaxProjectGrantQuery($filters);
        $minProjectGrant = $this->getMinProjectGrantQuery($filters);
        $medianProjectGrant = $this->getMedianProjectGrantQuery($filters);
        $dataMap = $this->getDataMapQueryResult($filters);

        return $this->getCleanedFinancialData($dataMap, $maxProjectGrant, $minProjectGrant, $medianProjectGrant);
    }

    /**
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDataMapQueryResult(array $filters)
    {
        $dataViewQuery = $this->dataFinancialMapQueryFactory->create($filters);

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($dataViewQuery);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getIndicatorMapData(array $filters)
    {
        $dataViewQuery = $this->dataIndicatorMapQueryFactory->create($filters);

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($dataViewQuery);
        $stmt->execute();

        return $this->getCleanedIndicatorData($stmt->fetchAll());
    }

    /**
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLocalizedInfos(array $filters)
    {
        $localizedInfos = $this->localizedInfoQueryFactory->create($filters);

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($localizedInfos);
        $stmt->execute();

        $data = $stmt->fetchAll();

        $localizedInfos = [];

        if (empty($data)) {
            return  $localizedInfos;
        }

        foreach ($data[0] as $key => $datum) {
            $localizedInfos[$key] = $datum;
        }
        return $localizedInfos;
    }

    /**
     * @param array $data
     * @param array $localizedInfos
     * @param float $max
     * @param float $min
     * @param float $median
     * @return array
     */
    protected function getCleanedFinancialData(array $data, float $max, float $min, float $median)
    {
        $cleanedData = $this->getCleanedDataHeader($max, $min, $median);

        $indexedCity = [];
        foreach ($data as $datum) {
            $indexedOf = array_search($datum['city_name'], $indexedCity);
            if ($indexedOf === false) {
                $indexedCity [] = $datum['city_name'];
                $indexedOf = count($indexedCity) - 1;

                $cleanedData['cities'][$indexedOf]['city_name'] = $datum['city_name'];
                $cleanedData['cities'][$indexedOf]['data'] = [];
                $cleanedData['cities'][$indexedOf]['import_models'] = [];

                if (!isset($cleanedData['cities'][$indexedOf]['lat'])) {
                    $cleanedData['cities'][$indexedOf]['lat'] = floatval($datum['lat']);
                }

                if (!isset($cleanedData['cities'][$indexedOf]['long'])) {
                    $cleanedData['cities'][$indexedOf]['long'] = floatval($datum['long']);
                }

                if (!isset($cleanedData['cities'][$indexedOf]['total_sum_grant'])) {
                    $cleanedData['cities'][$indexedOf]['total_sum_grant'] = floatval($datum['total_sum_grant']);
                }

                if (!isset($cleanedData['cities'][$indexedOf]['size'])) {
                    $cleanedData['cities'][$indexedOf]['size'] = $this->getSize(floatval($datum['total_sum_grant']), $max, $min);
                }

                if (!isset($cleanedData['cities'][$indexedOf]['city_id'])) {
                    $cleanedData['cities'][$indexedOf]['city_id'] = $datum['city_id'];
                }
            }
            $indexOfImportModelLegend = array_search($datum['import_model_name'], $cleanedData['legend']['import_models']);
            if ($indexOfImportModelLegend === false) {
                $cleanedData['legend']['import_models'][$datum['import_model_name']] []= $datum['import_model_color'];
            }

            $indexOfImportedModel = array_search($datum['import_model_name'], $cleanedData['cities'][$indexedOf]['import_models']);

            if ($indexOfImportedModel === false) {
                $cleanedData['cities'][$indexedOf]['import_models'] []= $datum['import_model_name'];
                $cleanedData['cities'][$indexedOf]['colors'] []= $datum['import_model_color'];
                $indexOfImportedModel = count($cleanedData['cities'][$indexedOf]['import_models']) - 1;
                $cleanedData['cities'][$indexedOf]['data'][$indexOfImportedModel] = 0;
            }

            $cleanedData['cities'][$indexedOf]['import_models'][$indexOfImportedModel] = $datum['import_model_name'];
            $cleanedData['cities'][$indexedOf]['data'][$indexOfImportedModel] += floatval($datum['sum_grant']);
        }

        return $cleanedData;
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
            'median' => ($max + $min)/2,
            'legend' => [
                'sizeMax' => self::SIZES['max'],
                'sizeMedian' => $this->getSize(($max/2), $max, $min),
                'sizeMin' => self::SIZES['min'],
                'import_models' => []
            ],
            'cities' => []
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getCleanedIndicatorData(array $data)
    {
        $cleanedData['legend']['import_models'] = [];
        for ($i = 0; $i < count($data); $i++) {
            if (!array_key_exists($data[$i]['import_model_name'], $cleanedData['legend']['import_models'])) {
                $cleanedData['legend']['import_models'][$data[$i]['import_model_name']] = null;
            }
            $data[$i]['lat'] = floatval($data[$i]['lat']);
            $data[$i]['long'] = floatval($data[$i]['long']);
        }
        $cleanedData['cities'] = $data;

        return $cleanedData;
    }

    /**
     * @param float $value
     * @param float $max
     * @param float $min
     * @return float|int
     */
    protected function getSize(float $value, float $max, float $min)
    {
        $delta = $max - $min;

        if ($delta == 0) {
            return self::SIZES['max'];
        }

        return ((($value - $min) / $delta) * (self::SIZES['max'] - self::SIZES['min'])) + self::SIZES['min'];
    }

    /**
     * @param array $filters
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getMaxProjectGrantQuery(array $filters)
    {
        $maxProjectQuery = $this->projectGrantQueryFactory->create(
            $filters,
            ProjectGrantQueryFactory::GET_MAX_PIE,
            ProjectGrantQueryFactory::MAX
        );

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($maxProjectQuery);
        $stmt->execute();

        return floatval($stmt->fetchAll()[0]["max"]);
    }

    /**
     * @param array $filters
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getMinProjectGrantQuery(array $filters)
    {
        $minProject =  $this->projectGrantQueryFactory->create(
            $filters,
            ProjectGrantQueryFactory::GET_MIN_PIE,
            ProjectGrantQueryFactory::MIN
        );

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($minProject);
        $stmt->execute();

        return floatval($stmt->fetchAll()[0]["min"]);
    }

    /**
     * @param array $filters
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getMedianProjectGrantQuery(array $filters)
    {
        $medianQuery = $this->projectGrantQueryFactory->create(
            $filters,
            ProjectGrantQueryFactory::GET_MEDIAN_PIE,
            ProjectGrantQueryFactory::MEDIAN
        );

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($medianQuery);
        $stmt->execute();

        return floatval($stmt->fetchAll()[0]["median"]);
    }
}
