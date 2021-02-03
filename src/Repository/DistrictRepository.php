<?php


namespace App\Repository;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DistrictRepository
 * @package App\Repository
 */
class DistrictRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, District::class);
    }

    /**
     * @param array $filters
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDistricts(array $filters)
    {

        $queryBuilder = $this->createQueryBuilder('district');

        $this
            ->addDepartmentFilter($filters, $queryBuilder)
            ->addMaxYearCityFilter($queryBuilder);


        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return DistrictRepository
     */
    private function addDepartmentFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['department']) || !$filters['department'] instanceof Department) {
            return $this;
        }
        $queryBuilder
            ->andWhere('district.department = :department')
            ->setParameter('department', $filters['department']->getId());

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return DistrictRepository
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function addMaxYearCityFilter(QueryBuilder &$queryBuilder)
    {
        $cityMaxYear = $this->_em->getRepository(City::class)->getMaxYear();

        $queryBuilder
            ->join('district.cities', 'cities')
            ->andWhere('cities.year = :year')
            ->setParameter('year', $cityMaxYear)
            ->groupBy('district.id', 'district.code', 'district.name', 'cities.year');

        return $this;
    }
}
