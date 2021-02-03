<?php


namespace App\Repository;

use App\Entity\ImportExport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * Class ImportExportRepository
 * @package App\Repository
 */
class ImportExportRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportExport::class);
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function findRunnableTaskByType(string $type)
    {
        $queryBuilder = $this->createQueryBuilder('import_export');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('import_export.state', [
                ImportExport::STATE_NEW,
                ImportExport::STATE_RUNNING
            ]))
            ->andWhere($queryBuilder->expr()->eq('import_export.type', $queryBuilder->expr()->literal($type)))
            ->andWhere($queryBuilder->expr()->eq('import_export.enabled', $queryBuilder->expr()->literal(true)))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function findErrorTaskByType(string $type)
    {
        $queryBuilder = $this->createQueryBuilder('import_export');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('import_export.state', [
                ImportExport::STATE_ERROR,
            ]))
            ->andWhere($queryBuilder->expr()->eq('import_export.type', $queryBuilder->expr()->literal($type)))
            ->andWhere($queryBuilder->expr()->eq('import_export.enabled', $queryBuilder->expr()->literal(true)))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findRunnableTaskByName(string $name)
    {
        $queryBuilder = $this->createQueryBuilder('import_export');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('import_export.state', [
                ImportExport::STATE_NEW,
                ImportExport::STATE_RUNNING
            ]))
            ->andWhere($queryBuilder->expr()->eq('import_export.name', $queryBuilder->expr()->literal($name)))
            ->andWhere($queryBuilder->expr()->eq('import_export.enabled', $queryBuilder->expr()->literal(true)))
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
