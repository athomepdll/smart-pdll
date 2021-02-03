<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class InseeManager
 * @package App\Manager
 */
class CityManager extends AbstractManager
{
    /**
     * @param Entity $city
     * @param bool $flush
     * @return mixed|void
     */
    public function saveEntity(Entity $city, $flush = true)
    {
        $this->entityManager->persist($city);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
