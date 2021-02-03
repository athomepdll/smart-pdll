<?php


namespace App\Manager;

use App\Entity\Columns\DataColumn;
use App\Entity\DataLine;
use App\Entity\Entity;
use AthomeSolution\ImportBundle\Entity\ColumnInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DataColumn
 * @package App\Manager
 */
class DataLineManager extends AbstractManager
{
    /**
     * @param Entity $dataLine
     * @param bool $flush
     * @return Entity
     */
    public function saveEntity(Entity $dataLine, $flush = true)
    {
        $this->entityManager->persist($dataLine);

        if ($flush === true) {
            $this->entityManager->flush();
        }

        return $dataLine;
    }

    /**
     * @param Entity $dataLine
     * @param bool $flush
     */
    public function removeEntity(Entity $dataLine, $flush = true)
    {
        $this->entityManager->remove($dataLine);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
