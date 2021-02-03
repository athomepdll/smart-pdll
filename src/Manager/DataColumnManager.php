<?php


namespace App\Manager;

use App\Entity\Columns\DataColumn;
use App\Entity\Entity;
use AthomeSolution\ImportBundle\Entity\ColumnInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DataColumn
 * @package App\Manager
 */
class DataColumnManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AbstractManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param ColumnInterface $dataColumn
     * @param bool $flush
     * @return ColumnInterface
     */
    public function saveEntity(ColumnInterface $dataColumn, $flush = true)
    {
        $this->entityManager->persist($dataColumn);

        if ($flush === true) {
            $this->entityManager->flush();
        }

        return $dataColumn;
    }

    /**
     * @param ColumnInterface $dataColumn
     * @param bool $flush
     */
    public function removeEntity(ColumnInterface $dataColumn, $flush = true)
    {
        $this->entityManager->remove($dataColumn);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
