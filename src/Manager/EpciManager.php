<?php


namespace App\Manager;

use App\Entity\Entity;
use App\Entity\Epci;

/**
 * Class GroupManager
 * @package App\Manager
 */
class EpciManager extends AbstractManager
{
    /**
     * @param Entity $group
     * @param bool $flush
     * @return mixed|void
     */
    public function saveEntity(Entity $group, $flush = true)
    {
        $this->entityManager->persist($group);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
