<?php


namespace AthomeSolution\ImportBundle\Services\FormatManager;


use AthomeSolution\ImportBundle\Services\FormatSpecification\AbstractFormatSpecification;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FormatManagerInterface
 * @package AthomeSolution\ImportBundle\Services\FormatManager
 */
interface FormatManagerInterface
{
    /**
     * @param AbstractFormatSpecification $specification
     * @return mixed
     */
    public function addChainBlock(AbstractFormatSpecification $specification);

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    public function getReader(\SplFileObject $file);

    /**
     * @return mixed
     */
    public function getResponsibilityChain();

    /**
     * @param $responsibilityChain
     */
    public function setResponsibilityChain($responsibilityChain): void;
}