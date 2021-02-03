<?php


namespace App\Repository;

use App\Entity\DataLine;
use App\Entity\DataType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataTypeRepository
 * @package App\Repository
 */
class DataLineRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DataLine::class);
    }
}
