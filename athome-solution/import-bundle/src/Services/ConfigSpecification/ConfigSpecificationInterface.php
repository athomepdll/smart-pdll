<?php


namespace AthomeSolution\ImportBundle\Services\ConfigSpecification;


/**
 * Interface ConfigSpecificationInterface
 * @package AthomeSolution\ImportBundle\Services\ConfigSpecification
 */
interface ConfigSpecificationInterface
{
    /**
     * @param AbstractConfigSpecification $specification
     * @return mixed
     */
    public function setSuccessor(AbstractConfigSpecification $specification);

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws \Exception
     */
    public function support(array $data, array &$objects);

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     */
    public function execute(array $data, array &$objects);
}