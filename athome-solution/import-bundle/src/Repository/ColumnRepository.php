<?php


namespace AthomeSolution\ImportBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AthomeSolution\ImportBundle\Entity\Column;

class ColumnRepository extends ServiceEntityRepository
{
    /**
     * ProjectRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Column::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('column');
    }

    /**
     * @param int $configId
     * @return mixed
     */
    public function getByConfig(int $configId)
    {
        $queryBuilder = $this->getBuilder();
        $queryBuilder
            ->where('column.config = :configId')
            ->indexBy('column', 'column.columnName')
            ->setParameter('configId', $configId);

        return $queryBuilder->getQuery()->getResult();
    }
}