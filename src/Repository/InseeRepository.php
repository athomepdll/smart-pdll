<?php


namespace App\Repository;

use App\Entity\City;
use App\Entity\Insee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class InseeRepository
 * @package App\Repository
 */
class InseeRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Insee::class);
    }
}
