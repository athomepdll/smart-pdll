<?php


namespace App\Repository;

use App\Entity\ImportLog;
use App\Entity\ImportModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * Class ImportLogRepository
 * @package App\Repository
 */
class ImportLogRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportLog::class);
    }

    /**
     * @param ImportModel $importModel
     * @param bool $isDisabled
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByImportModel(ImportModel $importModel, bool $isDisabled = false)
    {
        $queryBuilder = $this->createQueryBuilder('import_log');
        $queryBuilder
            ->andWhere('import_log.importModel = :importModel')
            ->andWhere('import_log.isDisabled = :isDisabled')
            ->setParameter('importModel', $importModel)
            ->setParameter('isDisabled', $isDisabled);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getYears()
    {
        $queryBuilder = $this->createQueryBuilder('import_log');

        $queryBuilder
            ->select('import_log.year')
            ->where(
                $queryBuilder->expr()->eq('import_log.isDisabled', $queryBuilder->expr()->literal(false))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('import_log.status', $queryBuilder->expr()->literal('success'))
            )
            ->orderBy('import_log.year')
            ->groupBy('import_log.year');


        return array_map('current', $queryBuilder->getQuery()->getArrayResult());
    }

    /**
     * @param string           $year
     * @param ImportModel|null $importModel
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByImportModelAndYear(string $year, ImportModel $importModel = null)
    {
        $queryBuilder = $this->createQueryBuilder('import_log');
        if ($importModel) {
            $queryBuilder
                ->andWhere('import_log.importModel = :importModel')
                ->andWhere('import_log.isDisabled = :isDisabled')
                ->setParameter('importModel', $importModel)
                ->setParameter('isDisabled', false);
        }
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('import_log.year', ':year'))
            ->setParameter('year', $year)
            ->orderBy('import_log.year');

        return $queryBuilder;
    }
}
