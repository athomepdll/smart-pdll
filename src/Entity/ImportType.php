<?php


namespace App\Entity;

/**
 * Class ImportType
 * @package App\Entity
 */
class ImportType extends Entity
{
    const FINANCIAL = 'financial';
    const INDICATOR = 'indicator';

    /** @var string */
    protected $name;
    /** @var string */
    protected $value;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
