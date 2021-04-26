<?php


namespace AthomeSolution\ImportBundle\Entity;


/**
 * Interface ColumnInterface
 * @package AthomeSolution\ImportBundle\Entity
 */
interface ColumnInterface
{
    /**
     * @return string
     */
    public function getColumnName(): ?string;

    /**
     * @param string $columnName
     */
    public function setColumnName(?string $columnName): void;

    /**
     * @return string
     */
    public function getClass(): ?string;

    /**
     * @param string $class
     */
    public function setClass(?string $class): void;

    /**
     * @return string
     */
    public function getMethod(): ?string;

    /**
     * @param string $method
     */
    public function setMethod(?string $method): void;

    /**
     * @return string
     */
    public function getAttribute(): ?string;
    /**
     * @param string $attribute
     */
    public function setAttribute(?string $attribute): void;

    /**
     * @return bool
     */
    public function getIdentifier(): ?string;

    /**
     * @param bool $identifier
     */
    public function setIdentifier(?string $identifier): void;


    /**
     * @return Config
     */
    public function getConfig(): ?Config;


    /**
     * @param Config $config
     */
    public function setConfig(?Config $config): void;

}