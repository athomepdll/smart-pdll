<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class DistrictManager
 * @package App\Manager
 */
class DistrictManager extends AbstractManager
{

    /**
     * @param Entity $entity
     * @param bool $flush
     */
    public function saveEntity(Entity $entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
