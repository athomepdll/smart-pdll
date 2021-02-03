<?php


namespace App\Repository;

use App\Entity\CarryingStructure;
use App\Entity\City;
use App\Entity\DataView;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\ImportModel;
use App\Entity\ImportType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ImportModelRepository
 * @package App\Repository
 */
class ImportModelRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportModel::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function generateQueryBuilder()
    {
        return $this->createQueryBuilder('import_model');
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getImportModels(array $filters)
    {
        $queryBuilder = $this->generateQueryBuilder();

        if (isset($filters['financialDomains']) && !empty($filters['financialDomains'])) {
            $this->addDomainsId($filters['financialDomains'], $queryBuilder);
        }

        if (isset($filters['domains']) && !empty($filters['domains'])) {
            $this->addDomainsId($filters['domains'], $queryBuilder);
        }

        $this
            ->addYearStart($filters, $queryBuilder)
            ->addYearEnd($filters, $queryBuilder)
            ->addFinancialDomains($filters, $queryBuilder)
            ->addIndicatorDomains($filters, $queryBuilder)
            ->addDepartmentFilter($filters, $queryBuilder)
            ->addDistrictFilter($filters, $queryBuilder)
            ->addEpciFilter($filters, $queryBuilder)
            ->addCityFilter($filters, $queryBuilder)
            ->addCarryingFilter($filters, $queryBuilder);

        $queryBuilder->addGroupBy('import_model.id');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getImportModelsSortedByDomains(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('import_model');
        $queryBuilder
            ->select('import_model.id as id', 'import_model.name as name', 'domain.name as domain_name')
            ->join('import_model.domains', 'domain')
            ->join('import_model.importType', 'import_type')
            ->andWhere('import_type.name = :importType')
            ->setParameters([
                'importType' => ImportType::INDICATOR
            ])
            ->groupBy('import_model.id', 'import_model.name', 'domain.name');

        $this
            ->addYearStart($filters, $queryBuilder)
            ->addYearEnd($filters, $queryBuilder)
            ->addDepartmentFilter($filters, $queryBuilder)
            ->addDistrictFilter($filters, $queryBuilder)
            ->addEpciFilter($filters, $queryBuilder)
            ->addCityFilter($filters, $queryBuilder)
            ->addCarryingFilter($filters, $queryBuilder);

        $result = $queryBuilder->getQuery()->getArrayResult();


        return $this->getCleanedData($result);
    }

    /**
     * @param array $domainsId
     * @param QueryBuilder $queryBuilder <
     */
    private function addDomainsId(array $domainsId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('import_model.id', $domainsId)
            );
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addFinancialDomains(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['isFinancial']) || $filters['isFinancial'] !== "true") {
            return $this;
        }

        $queryBuilder
            ->join('import_model.importType', 'import_type')
            ->andWhere('import_type.name = :importTypeFinancial')
            ->setParameter('importTypeFinancial', ImportType::FINANCIAL);

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addIndicatorDomains(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['isFinancial']) || $filters['isFinancial'] !== "false") {
            return $this;
        }

        $queryBuilder
            ->join('import_model.importType', 'import_type')
            ->andWhere('import_type.name = :importTypeIndicator')
            ->setParameter('importTypeIndicator', ImportType::INDICATOR);

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addYearStart(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['yearStart']) || $filters['yearStart'] === null) {
            return $this;
        }

        $this->addJoinDataView($queryBuilder);

        $operator = '=';

        if (isset($filters['yearEnd'])) {
            $operator = '>=';
        }

        $queryBuilder
            ->andWhere("data_view.year ${operator} :yearStart")
            ->setParameter('yearStart', $filters['yearStart']);

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return $this
     */
    private function addYearEnd(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['yearEnd']) || $filters['yearEnd'] === null) {
            return $this;
        }

        $this->addJoinDataView($queryBuilder);

        $queryBuilder
            ->andWhere("data_view.year <= :yearEnd")
            ->setParameter('yearEnd', $filters['yearEnd']);

        return $this;
    }


    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addDepartmentFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['department']) || !$filters['department'] instanceof Department) {
            return $this;
        }

        $department = $filters['department'];

        $this->addJoinDataView($queryBuilder);

        $queryBuilder->andWhere('data_view.department = :departmentId')
            ->setParameter('departmentId', $department->getId());

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addDistrictFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['district']) || !$filters['district'] instanceof District) {
            return $this;
        }

        $district = $filters['district'];

        $this->addJoinDataView($queryBuilder);

        $queryBuilder->andWhere('data_view.district = :districtId')
            ->setParameter('districtId', $district->getId());

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addEpciFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['epci']) || !$filters['epci'] instanceof Epci) {
            return $this;
        }

        $epci = $filters['epci'];

        $this->addJoinDataView($queryBuilder);

        $citiesId = $epci->getCities()->map(function (City $city) {
            return $city->getId();
        })->toArray();

        if (empty($city)) {
            return $this;
        }

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('data_view.city', $citiesId)
            );

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return ImportModelRepository
     */
    private function addCityFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['city']) || !$filters['city'] instanceof City) {
            return $this;
        }
        $city = $filters['city'];

        $this->addJoinDataView($queryBuilder);

        $queryBuilder
            ->join('data_view.city', 'city')
            ->andWhere(
                $queryBuilder->expr()->eq('city.siren', $queryBuilder->expr()->literal($city->getSiren()))
            );

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return $this
     */
    private function addCarryingFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['carryingStructure']) || !$filters['carryingStructure'] instanceof CarryingStructure) {
            return $this;
        }
        $carryingStructure = $filters['carryingStructure'];

        $this->addJoinDataView($queryBuilder);

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'data_view.sirenCarrier',
                    $queryBuilder->expr()->literal($carryingStructure->getSiren())
                )
            );

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function addJoinDataView(QueryBuilder &$queryBuilder)
    {
        if (!in_array('data_view', $queryBuilder->getAllAliases())) {
            $queryBuilder
                ->join(DataView::class, 'data_view', 'WITH', 'data_view.importModel = import_model.id');
        }
    }

    /**
     * @param array $result
     * @return array
     */
    private function getCleanedData(array $result)
    {
        $sorted = [];
        $indexed = [];
        foreach ($result as $data) {
            $index = array_search($data['domain_name'], $indexed);
            if ($index === false) {
                $indexed []= $data['domain_name'];
                $index = count($indexed) - 1;
                $sorted[$index]['domainName'] = $data['domain_name'];
            }
            $sorted[$index]['nodes'] [] = $data;
            $sorted[$index]['showChildren'] = true;
            $sorted[$index]['hasIncludedChildren'] = false;
            if (!isset($sorted[$index]['nodesIds']) || !array_search($data['id'], $sorted[$index]['nodesIds'])) {
                $sorted[$index]['nodesIds'] []= $data['id'];
            }
        }

        return $sorted;
    }
}
