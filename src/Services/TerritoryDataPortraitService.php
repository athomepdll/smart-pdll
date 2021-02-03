<?php


namespace App\Services;

use App\Entity\FinancialField;
use App\Repository\DataRepository;
use App\Repository\FinancialFieldRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TerritoryDataPortraitService
 * @package App\Services
 */
class TerritoryDataPortraitService extends DataViewService
{
    /**
     * DataViewService constructor.
     * @param DataRepository $dataRepository
     * @param FinancialFieldRepository $financialFieldRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(DataRepository $dataRepository, EntityManagerInterface $manager, FinancialFieldRepository $financialFieldRepository)
    {
        parent::__construct($dataRepository, $manager, $financialFieldRepository);
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
                $totalHt = floatval($data['total_ht']);
                $totalGrant = floatval($data['total_grant']);
                $indexOfCities [] = $data['full_name'];
                $index = count($indexOfCities) - 1;
                $cleanedData[$index]['fullName'] = $data['full_name'];
                $cleanedData[$index]['cityName'] = $data['city_name'];
                $cleanedData[$index]['cityId'] = $data['city_id'];
                $cleanedData[$index]['actualCityId'] = $data['actual_city_id'];
                $cleanedData[$index]['hasCityChanged'] = $data['has_city_changed'];
                $cleanedData[$index]['actualCityName'] = $data['actual_city_name'];
                $cleanedData[$index]['totalHt'] = number_format($totalHt, 2, ',', ' '); // . ' €'
                $cleanedData[$index]['totalGrant'] = number_format($totalGrant, 2, ',', ' '); // . ' €'
            }

            $cleanedData[$index]['headers'] = $columnsName;

            $this->addProjectCostIfFinancial($projectCostField, $data);
            $this->addGrantIfFinancial($grantNameField, $data);

            $cleanedData[$index]['data'] [] = $data;
        }

        return $cleanedData;
    }
}
