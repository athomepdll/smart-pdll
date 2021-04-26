<?php


namespace AthomeSolution\ImportBundle\Services\FormatSpecification;


/**
 * Class AbstractFormatSpecification
 * @package AthomeSolution\ImportBundle\Services\FormatSpecification
 */
abstract class AbstractFormatSpecification implements FormatSpecificationInterface
{
    /**
     * @var AbstractFormatSpecification
     */
    private $successor;

    /**
     * @param AbstractFormatSpecification $specification
     */
    public function setSuccessor(AbstractFormatSpecification $specification)
    {
        $this->successor = $specification;
    }
}