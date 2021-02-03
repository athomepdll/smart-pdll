<?php


namespace App\Repository;

use App\Entity\City;
use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RegionRepository
 * @package App\Repository
 */
class RegionRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Region::class);
    }
}
