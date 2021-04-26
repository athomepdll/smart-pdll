<?php


namespace AthomeSolution\ImportBundle\Services\ConfigManager;


use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Interface ConfigManagerInterface
 * @package AthomeSolution\ImportBundle\Services\ConfigManager
 */
interface ConfigManagerInterface
{
    /**
     * @return array|null
     */
    public function getColumns(): ?array;

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void;

    /**
     * @param array $columns
     */
    public function generateColumns(array $columns): void;
}