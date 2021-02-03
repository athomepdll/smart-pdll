<?php


namespace App\Entity;

/**
 * Class Enumeration
 * @package App\Entity
 */
class Enumeration extends Entity
{
    const HELP_PAGE = 'help_page';

    /** @var string */
    protected $name;
    /** @var string */
    protected $value;
    /** @var int */
    protected $position;
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
