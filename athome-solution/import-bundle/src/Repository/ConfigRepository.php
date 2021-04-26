<?php

namespace AthomeSolution\ImportBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AthomeSolution\ImportBundle\Entity\Config;

class ConfigRepository extends ServiceEntityRepository
{
    /**
     * ProjectRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('config');
    }

    public function getByConfigName(string $configName)
    {
        $queryBuilder = $this->getBuilder();
        $queryBuilder
            ->where('config.name = :configName')
            ->setParameter('configName', $configName);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}