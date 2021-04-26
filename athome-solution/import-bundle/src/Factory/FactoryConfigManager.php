<?php


namespace AthomeSolution\ImportBundle\Factory;


/**
 * Class FactoryManager
 * @package AthomeSolution\ImportBundle\Factory
 */
class FactoryConfigManager
{

    /** @var array */
    public $configInformation;

    /**
     * @param string $key
     * @param string $value
     */
    public function addConfigInformation(string $key, string $value)
    {
        $this->configInformation[$key] = $value;
    }

}