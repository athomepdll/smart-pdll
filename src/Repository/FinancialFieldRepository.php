<?php


namespace App\Repository;

use App\Entity\DataLevel;
use App\Entity\FinancialField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DataLevelRepository
 * @package App\Repository
 */
class FinancialFieldRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FinancialField::class);
    }
}
