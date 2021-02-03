<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class ImportModelManager
 * @package App\Manager
 */
class ImportModelManager extends AbstractManager
{

    /**
     * @param Entity $entity
     * @param bool $flush
     * @return Entity
     */
    public function saveEntity(Entity $entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param bool $flush
     * @return mixed|void
     */
    public function removeEntity(Entity $entity, $flush = true)
    {
        $this->entityManager->remove($entity);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
