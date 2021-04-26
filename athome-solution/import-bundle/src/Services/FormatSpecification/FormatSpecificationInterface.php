<?php


namespace AthomeSolution\ImportBundle\Services\FormatSpecification;


/**
 * Class FormatSpecificationInterface
 * @package AthomeSolution\ImportBundle\Services\FormatSpecification
 */
interface FormatSpecificationInterface
{
    /**
     * @param AbstractFormatSpecification $specification
     * @return mixed
     */
    public function setSuccessor(AbstractFormatSpecification $specification);
}