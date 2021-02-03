<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class InseeManager
 * @package App\Manager
 */
class InseeManager extends AbstractManager
{
    /**
     * @param Entity $insee
     * @param bool $flush
     * @return mixed|void
     */
    public function saveEntity(Entity $insee, $flush = true)
    {
        $this->entityManager->persist($insee);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
