<?php


namespace AthomeSolution\ImportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Config
 * @package AthomeSolution\ImportBundle\Entity
 */
class Config extends Entity
{
    /** @var string */
    public $name;

    /** @var string */
    public $pattern;

    /** @var Collection */
    public $columns;

    public function __construct()
    {
        $this->columns = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }
}