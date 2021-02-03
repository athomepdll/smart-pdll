<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Interface ManagerInterface
 * @package App\Manager
 */
interface ManagerInterface
{
    /**
     * @param Entity $entity
     * @param bool $flush
     * @return mixed
     */
    public function saveEntity(Entity $entity, $flush = true);

    /**
     * @param Entity $entity
     * @param bool $flush
     * @return mixed
     */
    public function removeEntity(Entity $entity, $flush = true);
}
