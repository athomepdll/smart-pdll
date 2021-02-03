<?php


namespace App\Repository;

use App\Entity\Data;
use App\Entity\ImportModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use mysqli;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataRepository
 * @package App\Repository
 */
class DataRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Data::class);
    }

    /**
     * @param array $columnsName
     * @param string $dataLevel
     * @param int $importModelId
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCrosstabedView(array $columnsName, string $dataLevel, int $importModelId)
    {
        $headers = 'data_line int, data_view_line int, import_model_name varchar';

        foreach ($columnsName as $columnName) {
            $headers .= ', "' . $columnName . '" varchar';
        }

        $sourceQuery = "$$ SELECT data_line_id, data_view_id, import_model_name, column_name, value FROM data_view WHERE data_level =  '" . $dataLevel . "' AND import_model_id = " . $importModelId . " order by 1 $$";
        $categoryQuery = " $$ SELECT column_name FROM data_view WHERE import_model_id = " . $importModelId . " AND data_level = '" . $dataLevel . "' GROUP BY column_name ORDER BY SUM(data_line_id + data_view_id) $$";

        $sql = "select * from crosstab(" . $sourceQuery . "," . $categoryQuery . ") as ct(" . $headers . ")";

        $stmt = $this->_em
            ->getConnection()
            ->prepare($sql)
        ;

        $stmt->execute();

        $array = [];
        foreach ($stmt->fetchAll() as $data) {
            $array[$data['import_model_name']] [] = $data;
        }

        return $array;
    }

    /**
     * @param ImportModel $importModel
     * @param string $dataLevel
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getHeadersColumn(ImportModel $importModel, string $dataLevel)
    {
        $sql = "SELECT column_name FROM data_view WHERE import_model_id = '" . $importModel->getId() . "'";
        $sql .= "AND data_level = '" . $dataLevel . "' GROUP BY column_name ORDER BY SUM(data_line_id + data_view_id)";
        $statement = $this->_em
            ->getConnection()
            ->prepare($sql);

        $statement->execute();

        return array_map('current', $statement->fetchAll());
    }

    /**
     * @param ImportModel $importModel
     * @param array $dataLevels
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getHeadersColumnByDataLevels(ImportModel $importModel, array $dataLevels)
    {
        $dataLevels = array_map(function ($dataLevel) {
            return "'{$dataLevel}'";
        }, $dataLevels);

        $sql = "SELECT column_name FROM data_view";
        $sql .= " WHERE import_model_id = '" . $importModel->getId() . "' AND data_level in (" . join(', ', $dataLevels) . ")";
        $sql .=  " GROUP BY column_name ORDER BY SUM(data_line_id + data_view_id)";
        $statement = $this->_em
            ->getConnection()
            ->prepare($sql);

        $statement->execute();

        return array_map('current', $statement->fetchAll());
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getDataByFilters(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('data');
        $queryBuilder
            ->select('data.name as field', 'data.value as value')
            ->join('data.dataLine', 'dataLine')
            ->join('data.dataLevel', 'dataLevel')
            ->join('dataLine.importLog', 'importLog')
            ->join('importLog.importModel', 'importModel')
            ->leftJoin('dataLine.sirenRoot', 'city')
            ->leftJoin('city.epcis', 'epci')
            ->addGroupBy('data.name', 'data.value');

        if (isset($filters['dataLevel']) && $filters['dataLevel']) {
            $this->addDataLevel($filters['dataLevel'], $queryBuilder);
        }

        if (isset($filters['dataLine']) && $filters['dataLine'] !== null) {
            $this->addDataLine($filters['dataLine'], $queryBuilder);
        }

        if (isset($filters['yearStart']) && isset($filters['yearEnd'])) {
            $this->addYearStartAndStop($filters['yearStart'], $filters['yearEnd'], $queryBuilder);
        }

        if (isset($filters['department'])) {
            $this->addDepartmentFilter($filters['department'], $queryBuilder);
        }

        if (isset($filters['district'])) {
            $this->addDistrict($filters['district'], $queryBuilder);
        }

        if (isset($filters['epci'])) {
            $this->addEpci($filters['epci'], $queryBuilder);
        }

        if (isset($filters['city'])) {
            $this->addCity($filters['city'], $queryBuilder);
        }

        if (isset($filters['carryingStructure'])) {
            $this->addCarryingStructure($filters['carryingStructure'], $queryBuilder);
        }

        if (isset($filters['indicatorImportModels'])) {
            $this->addDomains($filters['indicatorImportModels'], $queryBuilder);
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param string $dataLevel
     * @param QueryBuilder $queryBuilder
     */
    private function addDataLevel(string $dataLevel, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('dataLevel.name = :dataLevel')
            ->setParameter('dataLevel', $dataLevel);
    }

    /**
     * @param int $dataLine
     * @param QueryBuilder $queryBuilder
     */
    private function addDataLine(int $dataLine, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('dataLine.id = :dataLine')
            ->setParameter('dataLine', $dataLine);
    }

    /**
     * @param int $yearStart
     * @param int $yearEnd
     * @param QueryBuilder $queryBuilder
     */
    private function addYearStartAndStop(int $yearStart, $yearEnd, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('importLog.year >= :yearStart')
            ->setParameter('yearStart', $yearStart);

        if ($yearEnd !== null) {
            $queryBuilder
                ->andWhere('importLog.year <= :yearEnd')
                ->setParameter('yearEnd', $yearEnd);
        }
    }

    /**
     * @param int $departmentId
     * @param QueryBuilder $queryBuilder
     */
    private function addDepartmentFilter(int $departmentId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('importLog.department = :department')
            ->setParameter('department', $departmentId);
    }

    /**
     * @param int $districtId
     * @param QueryBuilder $queryBuilder
     */
    private function addDistrict(int $districtId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('city.district = :district')
            ->setParameter('district', $districtId);
    }

    /**
     * @param int $epciId
     * @param QueryBuilder $queryBuilder
     */
    private function addEpci(int $epciId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('city.epcis', $epciId)
            );
    }

    /**
     * @param int $cityId
     * @param QueryBuilder $queryBuilder
     */
    private function addCity(int $cityId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('city.id = :cityId')
            ->setParameter('cityId', $cityId);
    }

    /**
     * @param string $sirenCarrier
     * @param QueryBuilder $queryBuilder
     */
    private function addCarryingStructure(string $sirenCarrier, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere('dataLine.sirenCarrier = :sirenCarrier')
            ->setParameter('sirenCarrier', $sirenCarrier);
    }

    /**
     * @param array $domainsId
     * @param QueryBuilder $queryBuilder
     */
    private function addDomains(array $domainsId, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('importModel.domains', $domainsId)
            );
    }
}
