<?php


namespace App\Repository;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\SirenCarrierColumn;
use App\Entity\Columns\SirenRootColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataColumnRepository
 * @package App\Repository
 */
class DataColumnRepository extends ServiceEntityRepository
{

    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DataColumn::class);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getDataColumns(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('data_column');
        $queryBuilder
            ->select(
                'data_column.id',
                'data_column.columnName',
                'data_column.attribute',
                "data_level.id as dataLevel",
                "data_type.id as dataType",
                'data_column.identifier',
                'data_column.method'
            )
            ->leftJoin('data_column.dataLevel', 'data_level')
            ->leftJoin('data_column.dataType', 'data_type');
        if (isset($filters['config']) && $filters['config'] != null) {
            $this->addConfigFilter($queryBuilder, $filters['config']);
        }

        if (isset($filters['notDefault']) && is_bool($filters['notDefault'])) {
            $this->addNotDefaultFilter($queryBuilder, $filters['notDefault']);
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getDataColumnsName(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('data_column');
        $queryBuilder
            ->select(
                'data_column.columnName'
            )
            ->leftJoin('data_column.dataLevel', 'data_level')
            ->leftJoin('data_column.dataType', 'data_type');
        if (isset($filters['config']) && $filters['config'] != null) {
            $this->addConfigFilter($queryBuilder, $filters['config']);
        }

        if (isset($filters['notDefault']) && is_bool($filters['notDefault'])) {
            $this->addNotDefaultFilter($queryBuilder, $filters['notDefault']);
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $configId
     */
    private function addConfigFilter(QueryBuilder &$queryBuilder, int $configId)
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq('data_column.config', $configId)
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $default
     */
    private function addNotDefaultFilter(QueryBuilder &$queryBuilder, bool $default)
    {
        $queryBuilder
            ->andWhere('data_column.identifier != :sirenCarrier')
            ->andWhere('data_column.identifier != :sirenRoot')
            ->setParameter('sirenCarrier', SirenCarrierColumn::SIREN_CARRIER_IDENTIFIER)
            ->setParameter('sirenRoot', SirenRootColumn::SIREN_ROOT_IDENTIFIER);
    }
}
