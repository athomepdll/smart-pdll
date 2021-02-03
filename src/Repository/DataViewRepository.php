<?php


namespace App\Repository;

use App\Entity\DataView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataViewRepository
 * @package App\Repository
 */
class DataViewRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DataView::class);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function cgetDataView(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('data_view');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
