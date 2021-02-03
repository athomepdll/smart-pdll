<?php


namespace App\Repository;

use App\Entity\Department;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param Department $department
     * @return mixed
     */
    public function getByPreferencesOrNull(Department $department)
    {
        $queryBuilder = $this->createQueryBuilder('user');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('user.department', $department->getId()),
                    $queryBuilder->expr()->isNull('user.department')
                )
            );

        return $queryBuilder->getQuery()->getResult();
    }
}
