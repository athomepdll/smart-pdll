<?php


namespace AthomeSolution\ImportBundle\Entity;


/**
 * Class Entity
 * @package AthomeSolution\ImportBundle\Entity
 */
class Entity
{
    /** @var int */
    public $id;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}