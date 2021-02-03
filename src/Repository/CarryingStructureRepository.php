<?php


namespace App\Repository;

use App\Entity\CarryingStructure;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CarryingStructureRepository
 * @package App\Repository
 */
class CarryingStructureRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CarryingStructure::class);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function cgetAction(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('carrying_structure');

        $queryBuilder->select(
            'carrying_structure.name',
            'carrying_structure.siren'
        );


        $queryBuilder
            ->addSelect('(SELECT MAX(carrying.year) FROM App\Entity\CarryingStructure carrying) as max_year')
            ->addSelect('(SELECT MAX(c.year) 
                FROM App\Entity\CarryingStructure c
                where c.siren = carrying_structure.siren and c.name = carrying_structure.name) AS max_current_year');

        $this
            ->addDepartmentFilter($filters, $queryBuilder)
            ->addDistrictFilter($filters, $queryBuilder)
            ->addEpciFilter($filters, $queryBuilder)
            ->addCityFilter($filters, $queryBuilder);

        $queryBuilder
            ->addOrderBy('carrying_structure.name')
            ->addGroupBy(
                'carrying_structure.siren',
                'carrying_structure.name'
            );

        return array_map(function (array $data) {
            $name = $data['max_current_year'] === $data['max_year']
                ? $data['name'] : $data['name'] . ' (' . $data['max_current_year'] . ')';
            return [
                'name' => $name,
                'siren' => $data['siren']
            ];
        }, $queryBuilder->getQuery()->getArrayResult());
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return CarryingStructureRepository
     */
    private function addDepartmentFilter(array $filters, QueryBuilder &$queryBuilder)
    {

        if (!isset($filters['department']) || !$filters['department'] instanceof Department) {
            return $this;
        }

        $queryBuilder
            ->andWhere('carrying_structure.department = :departmentId')
            ->setParameter('departmentId', $filters['department']->getId());

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return CarryingStructureRepository
     */
    private function addDistrictFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['district']) || !$filters['district'] instanceof District) {
            return $this;
        }

        $queryBuilder
            ->andWhere('carrying_structure.district = :districtId')
            ->setParameter('districtId', $filters['district']->getId());

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return CarryingStructureRepository
     */
    private function addEpciFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['epci']) || !$filters['epci'] instanceof Epci) {
            return $this;
        }
        $orX = $queryBuilder->expr()->orX();
        $conditionsOrX = [
            $queryBuilder->expr()->eq('carrying_structure.siren', $queryBuilder->expr()->literal($filters['epci']->getSiren())),
        ];
        $sirens = $filters['epci']->getCities()->map(function (City $city) {
            return $city->getSiren();
        })->toArray();
        if (!empty($sirens)) {
            $conditionsOrX [] = $queryBuilder->expr()->in('carrying_structure.siren', $sirens);
        }
        $orX->addMultiple($conditionsOrX);

        $queryBuilder
            ->andWhere(
                $orX
            );

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return CarryingStructureRepository
     */
    private function addCityFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['city']) || !$filters['city'] instanceof City) {
            return $this;
        }

        $queryBuilder
            ->andWhere('carrying_structure.siren = :citySiren')
            ->setParameter('citySiren', $filters['city']->getSiren());

        return $this;
    }
}
