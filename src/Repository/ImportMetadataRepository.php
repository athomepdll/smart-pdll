<?php


namespace App\Repository;

use App\Entity\ImportLog;
use App\Entity\ImportMetadata;
use App\Entity\ImportModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ImportLogRepository
 * @package App\Repository
 */
class ImportMetadataRepository extends ServiceEntityRepository
{
    /**
     * Repository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportMetadata::class);
    }
}
