<?php


namespace App\Repository;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\Insee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CityRepository
 * @package App\Repository
 */
class CityRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @param array $filters
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCities(array $filters)
    {

        $queryBuilder = $this->createQueryBuilder('city');

        $queryBuilder->select('city.siren', 'city.name');

        if (isset($filters['district']) && $filters['district'] instanceof District) {
            $this->addDistrictFilter($filters['district'], $queryBuilder);
        }

        if (isset($filters['epci']) && $filters['epci'] instanceof Epci) {
            $this->addEpciFilter($filters['epci'], $queryBuilder);
        }

        if (isset($filters['actualCity']) && $filters['actualCity'] instanceof City) {
            $this->addActualCityFilter($filters['actualCity'], $queryBuilder);
        }

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq('city.year', $this->getMaxYear())
            )
            ->addOrderBy('city.name')
            ->addGroupBy('city.name', 'city.siren');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param int $year
     * @return mixed
     */
    public function getByYear(int $year)
    {
        $queryBuilder = $this->createQueryBuilder('city');

        $queryBuilder
            ->select(
                "'Pays-de-la-Loire' as Region",
                'department.name as departement_nom',
                'department.code as departement_numero',
                'district.name as arrondissement_nom',
                'district.code as arrondissement_numero',
                'epcis.name as EPCI_nom',
                "COALESCE(insee_siren.insee, '') as EPCI_INSEE",
                'epcis.siren as EPCI_SIREN',
                'city.name as commune_nom',
                'city.insee as commune_INSEE',
                'city.siren as commune_SIREN',
                'actualCity.name as nouvelle_commune_nom',
                'actualCity.insee as nouvelle_commune_INSEE',
                'actualCity.siren as nouvelle_commune_SIREN'
            )
            ->leftJoin('city.actualCity', 'actualCity')
            ->join('city.district', 'district')
            ->join('district.department', 'department')
            ->join('city.epcis', 'epcis')
            ->leftJoin(Insee::class, 'insee_siren', 'WITH', 'insee_siren.siren = epcis.siren')
            ->andWhere('city.year = :year')
            ->setParameter('year', $year);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMaxYear()
    {
        $max = $this->createQueryBuilder('city_max');
        $max->select('MAX(city_max.year)');

        return $max->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getYears()
    {
        $queryBuilder = $this->createQueryBuilder('city_year');
        $queryBuilder
            ->select('city_year.year')
            ->groupBy('city_year.year')
            ->orderBy('city_year.year', 'ASC')
            ->indexBy('city_year', 'city_year.year');

        return array_map('current', $queryBuilder->getQuery()->getArrayResult());
    }

    /**
     * @param $cityId
     * @return array
     */
    public function getCity($cityId)
    {
        $queryBuilder = $this->createQueryBuilder('city');
        $queryBuilder
            ->andWhere('city.id = :cityId')
            ->setParameter('cityId', $cityId);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param District $district
     * @param QueryBuilder $queryBuilder
     */
    private function addDistrictFilter(District $district, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->join('city.district', 'district')
            ->andWhere('district.id = :district')
            ->setParameter('district', $district->getId());
    }

    /**
     * @param Epci $epci
     * @param QueryBuilder $queryBuilder
     */
    private function addEpciFilter(Epci $epci, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->join('city.epcis', 'epci')
            ->andWhere('epci.id = :epci')
            ->setParameter('epci', $epci->getId());
    }

    /**
     * @param City $city
     * @param QueryBuilder $queryBuilder
     */
    public function addActualCityFilter(City $city, QueryBuilder &$queryBuilder)
    {
        $queryBuilder
            ->addSelect('city.year', 'city.name as owner', 'actual_city.name', 'actual_city.year')
            ->innerJoin(City::class, 'actual_city', 'WITH', 'city.id = actual_city.actualCity')
            ->andWhere('city.id = :actualCityId')
            ->addOrderBy('city.year', 'DESC')
            ->setParameter('actualCityId', $city->getId())
            ->addGroupBy('city.year', 'city.name', 'actual_city.name', 'actual_city.year');
    }
}
