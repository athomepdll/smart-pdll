<?php


namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DepartmentRepository
 * @package App\Repository
 */
class DepartmentRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Department::class);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getDepartments(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('department');


        return $queryBuilder->getQuery()->getArrayResult();
    }
}
