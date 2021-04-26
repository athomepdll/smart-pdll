<?php


namespace AthomeSolution\ImportBundle\Entity;

/**
 * Class Column
 * @package AthomeSolution\ImportBundle\Entity
 */
class Column extends Entity implements ColumnInterface
{
    /** @var string */
    protected $columnName;

    /** @var string */
    protected $class;

    /** @var string */
    protected $method;

    /** @var string */
    protected $attribute;

    /** @var string */
    protected $identifier;

    /** @var Config */
    protected $config;

    /**
     * @return string
     */
    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     */
    public function setColumnName(?string $columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(?string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute(?string $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @return bool
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param bool $identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return Config
     */
    public function getConfig(): ?Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(?Config $config): void
    {
        $this->config = $config;
    }
}