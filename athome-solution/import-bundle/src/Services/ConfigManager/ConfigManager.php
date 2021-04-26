<?php


namespace AthomeSolution\ImportBundle\Services\ConfigManager;

use AthomeSolution\ImportBundle\Entity\Column;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class ConfigManager
 * @package AthomeSolution\ImportBundle\Services\ConfigManager
 */
class ConfigManager implements ConfigManagerInterface
{
    /** @var array */
    private $columns;

    /**
     * @return array
     */
    public function getColumns(): ?array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @param array $columns
     */
    public function generateColumns(array $columns): void
    {
        foreach ($columns as $column) {
            $className = $column['type'];
            $object = new $className();
            if ($object instanceof Column) {
                foreach ($column as $key => $value) {
                    if (method_exists($object, 'set' . strtoupper($key))) {
                        call_user_func_array([$object, 'set' . strtoupper($key)], [$value]);
                    }
                }
                $this->columns['identifier'] = $object;
            }
        }
    }
}