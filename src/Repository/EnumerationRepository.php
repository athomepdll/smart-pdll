<?php


namespace App\Repository;

use App\Entity\DataLevel;
use App\Entity\DataType;
use App\Entity\Enumeration;
use App\Entity\FinancialField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataLevelRepository
 * @package App\Repository
 */
class EnumerationRepository extends ServiceEntityRepository
{
    const ENUMERATION_CLASS = [
        'enum' => Enumeration::class,
        'data_level' => DataLevel::class,
        'data_type' => DataType::class,
        'financial_field' => FinancialField::class
    ];

    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Enumeration::class);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getArrayEnumerations(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('enumeration');

        $this
            ->addClassFilter($filters, $queryBuilder)
            ->addNameFilter($filters, $queryBuilder)
        ;


        $queryBuilder->addOrderBy('enumeration.position');

        return $queryBuilder->getQuery()->getArrayResult();
    }


    /**
     * @param array $filters
     * @return mixed
     */
    public function getEnumerations(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('enumeration');

        $this
            ->addClassFilter($filters, $queryBuilder)
            ->addNameFilter($filters, $queryBuilder)
        ;


        $queryBuilder->addOrderBy('enumeration.position');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEnumeration(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('enumeration');

        $this
            ->addClassFilter($filters, $queryBuilder)
            ->addNameFilter($filters, $queryBuilder)
        ;


        $queryBuilder->addOrderBy('enumeration.position');

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return EnumerationRepository
     */
    private function addClassFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['discr']) || $filters['discr'] === null
            || !isset(self::ENUMERATION_CLASS[$filters['discr']])) {
            return $this;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->isInstanceOf('enumeration', self::ENUMERATION_CLASS[$filters['discr']])
        );

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return $this
     */
    private function addNameFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['name'])) {
            return $this;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq('enumeration.name', $queryBuilder->expr()->literal($filters['name']))
        );

        return $this;
    }
}
