<?php


namespace App\Entity;

/**
 * Class Entity
 * @package App\Entity
 */
class Entity
{

    protected $id;

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
}
