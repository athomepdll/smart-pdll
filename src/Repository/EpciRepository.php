<?php


namespace App\Repository;

use App\Entity\District;
use App\Entity\Epci;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class GroupRepository
 * @package App\Repository
 */
class EpciRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Epci::class);
    }

    /**
     * @param array $filters
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEpcis(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder('epci');

        $queryBuilder->select('epci.name', 'epci.siren');

        $this
            ->addIsOwnTaxFilter($filters, $queryBuilder)
            ->addDistrictFilter($filters, $queryBuilder);

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq('epci.year', $this->getMaxYear())
            )
            ->addOrderBy('epci.name')
            ->addGroupBy('epci.siren', 'epci.name');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMaxYear()
    {
        $max = $this->createQueryBuilder('epci_max');
        $max->select('MAX(epci_max.year)');

        return $max->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return EpciRepository
     */
    private function addIsOwnTaxFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['isOwnTax']) || !is_bool($filters['isOwnTax'])) {
            return $this;
        }

        $queryBuilder
            ->andWhere('epci.isOwnTax = :isOwnTax')
            ->setParameter('isOwnTax', $filters['isOwnTax']);

        return $this;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @return EpciRepository
     */
    private function addDistrictFilter(array $filters, QueryBuilder &$queryBuilder)
    {
        if (!isset($filters['district']) || !$filters['district'] instanceof District) {
            return $this;
        }

        $queryBuilder
            ->join('epci.districts', 'district')
            ->andWhere('district.id = :districtId')
            ->setParameter('districtId', $filters['district']->getId());

        return $this;
    }
}
