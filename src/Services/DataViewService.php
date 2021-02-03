<?php


namespace App\Services;

use App\Entity\DataLevel;

use App\Entity\FinancialField;
use App\Entity\ImportModel;
use App\Factory\Query\ColumnsHeaderFactory;
use App\Factory\Query\DataViewFactory;
use App\Repository\DataRepository;
use App\Repository\FinancialFieldRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DataViewService
 * @package App\Services
 */
class DataViewService
{
    private const HEADERS = [
        'data_line int',
        'data_view_line int',
        'city_id int',
        'full_name varchar',
        'actual_city_id int',
        'carrier_name varchar',
        'has_city_changed int',
        'actual_city_name varchar',
        'year int',
        'city_name varchar',
        'import_model_name varchar',
        'total_ht numeric',
        'total_grant numeric'
    ];

    /**
     * @var EntityManagerInterface
     */
    protected $manager;
    /**
     * @var DataRepository
     */
    protected $dataRepository;
    /**
     * @var FinancialFieldRepository
     */
    protected $financialFieldRepository;

    /** @var DataViewFactory */
    protected $dataViewQueryFactory;

    /** @var ColumnsHeaderFactory */
    protected $columnsHeadersQueryFactory;
    /**
     * DataViewService constructor.
     * @param DataRepository $dataRepository
     * @param FinancialFieldRepository $financialFieldRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        DataRepository $dataRepository,
        EntityManagerInterface $manager,
        FinancialFieldRepository $financialFieldRepository
    ) {
        $this->dataRepository = $dataRepository;
        $this->manager = $manager;
        $this->financialFieldRepository = $financialFieldRepository;
        $this->dataViewQueryFactory = new DataViewFactory();
        $this->columnsHeadersQueryFactory = new ColumnsHeaderFactory();
    }

    /**
     * @param ImportModel $importModel
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createCrosstabView(ImportModel $importModel, array $filters)
    {
        $columnsName = $this->dataRepository->getHeadersColumn($importModel, DataLevel::SUMMARY);
        $filters['importModel'] = $importModel;

        $headers = $this->getCrosstabHeaders($columnsName);

        return $this->getCleanedData($columnsName, $this->getCrosstabedView($headers, $filters));
    }

    /**
     * @param string $headers
     * @param array $filters
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCrosstabedView(string $headers, array $filters)
    {
        $sourceQuery = $this->dataViewQueryFactory->create($filters);
        $categoryQuery = $this->columnsHeadersQueryFactory->create($filters);

        $sql = "select * from crosstab($$" . $sourceQuery . "$$,$$" . $categoryQuery . "$$) as ct(" . $headers . ")";

        $stmt = $this->manager
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $columnsName
     * @return string
     */
    protected function getCrosstabHeaders(array $columnsName)
    {
        $headers = join(', ', self::HEADERS);

        foreach ($columnsName as $columnName) {
            $type = isset(FinancialField::TYPE[$columnName]) ? FinancialField::TYPE[$columnName] : 'varchar';
            $headers .= ', "' . $columnName . '" ' . $type;
        }

        return $headers;
    }

    /**
     * @param array $columnsName
     * @param array $dataSet
     * @return array
     */
    protected function getCleanedData(array $columnsName, array $dataSet)
    {
        $projectCostField = $this->financialFieldRepository->findOneBy(['name' => FinancialField::PROJECT_COST_HT]);
        $grantNameField = $this->financialFieldRepository->findOneBy(['name' => FinancialField::GRANT]);
        $cleanedData = [];
        $indexOfCities = [];
        foreach ($dataSet as $data) {
            $index = array_search($data['full_name'], $indexOfCities);

            if ($index === false) {
                $totalHt = $data['total_ht'] === null ? null : floatval($data['total_ht']);
                $totalGrant = $data['total_grant'] === null ? null : floatval($data['total_grant']);
                $indexOfCities [] = $data['full_name'];
                $index = count($indexOfCities) - 1;
                $cleanedData[$index]['fullName'] = $data['full_name'];
                $cleanedData[$index]['cityName'] = $data['city_name'];
                $cleanedData[$index]['cityId'] = $data['city_id'];
                $cleanedData[$index]['actualCityId'] = $data['actual_city_id'];
                $cleanedData[$index]['hasCityChanged'] = $data['has_city_changed'];
                $cleanedData[$index]['actualCityName'] = $data['actual_city_name'];
                $cleanedData[$index]['totalHt'] = $totalHt === null ? null : number_format($totalHt, 2, ',', ' '); // . ' €'
                $cleanedData[$index]['totalGrant'] = $totalGrant === null ? null : number_format($totalGrant, 2, ',', ' '); // . ' €'
            }

            $cleanedData[$index]['headers'] = $columnsName;

            $this->addProjectCostIfFinancial($projectCostField, $data);
            $this->addGrantIfFinancial($grantNameField, $data);

            $cleanedData[$index]['data'] [] = $data;
        }

        return $cleanedData;
    }

    /**
     * @param $projectCostField
     * @param array $data
     */
    protected function addProjectCostIfFinancial($projectCostField, array &$data)
    {
        if ($projectCostField instanceof  FinancialField && isset($data[$projectCostField->getValue()])) {
            $data[$projectCostField->getValue()] = floatval($data[$projectCostField->getValue()]);
        }
    }

    /**
     * @param $grantNameField
     * @param array $data
     */
    protected function addGrantIfFinancial($grantNameField, array &$data)
    {
        if ($grantNameField instanceof  FinancialField && isset($data[$grantNameField->getValue()])) {
            $data[$grantNameField->getValue()] = floatval($data[$grantNameField->getValue()]);
        }
    }
}
