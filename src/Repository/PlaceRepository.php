<?php


namespace App\Repository;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class PlaceRepository
 * @package App\Repository
 */
class PlaceRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Place::class);
    }

    /**
     * @return array
     */
    public function getPlaces()
    {
        $queryBuilder = $this->createQueryBuilder('place');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getCentroidPlace(array $filters)
    {
        $query = $this->_em->createQuery(
            "SELECT ST_X(ST_Centroid(p.polygons)) as long, ST_Y(ST_Centroid(p.polygons)) as lat FROM App\Entity\Place p WHERE p.code = :code"
        );
        $code = $this->getCode($filters);

        if ($code === null) {
            return $code;
        }
        $query->setParameter('code', $code);

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $query->getResult();
        }
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getCode(array $filters)
    {
        if (isset($filters['code'])) {
            return $filters['code'];
        }

        if (isset($filters['city']) && $filters['city'] instanceof City) {
            return $filters['city']->getInsee();
        }

        if (isset($filters['epci']) && $filters['epci'] instanceof Epci) {
            return $filters['epci']->getSiren();
        }

        if (isset($filters['district']) && $filters['district'] instanceof District) {
            return $filters['district']->getCode();
        }

        if (isset($filters['department']) && $filters['department'] instanceof Department) {
            return $filters['department']->getCode();
        }
    }
}
