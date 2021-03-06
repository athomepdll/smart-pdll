<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class ImportLogManager
 * @package App\Manager
 */
class ImportLogManager extends AbstractManager
{
    /**
     * @param Entity $importLog
     * @param bool $flush
     * @return void
     */
    public function saveEntity(Entity $importLog, $flush = true)
    {
        $this->entityManager->persist($importLog);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Entity $entity
     * @param bool $flush
     * @return mixed|void
     */
    public function removeEntity(Entity $entity, $flush = true)
    {
        parent::removeEntity($entity, $flush); // TODO: Change the autogenerated stub
    }
}
