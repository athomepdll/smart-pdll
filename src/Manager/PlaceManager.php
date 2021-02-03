<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class PlaceManager
 * @package App\Manager
 */
class PlaceManager extends AbstractManager
{
    /**
     * @param Entity $place
     * @param bool $flush
     * @return mixed|void
     */
    public function saveEntity(Entity $place, $flush = true)
    {
        $this->entityManager->persist($place);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
