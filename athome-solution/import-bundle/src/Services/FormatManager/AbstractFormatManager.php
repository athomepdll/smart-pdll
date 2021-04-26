<?php


namespace AthomeSolution\ImportBundle\Services\FormatManager;


use AthomeSolution\ImportBundle\Services\FormatSpecification\AbstractFormatSpecification;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AbstractFormatManager
 * @package AthomeSolution\ImportBundle\Services\FormatManager
 */
abstract class AbstractFormatManager implements FormatManagerInterface
{
    /** @var array */
    private $responsibilityChain;

    /**
     * @return mixed
     */
    public function getResponsibilityChain()
    {
        return $this->responsibilityChain;
    }

    /**
     * @param mixed $responsibilityChain
     */
    public function setResponsibilityChain($responsibilityChain): void
    {
        $this->responsibilityChain = $responsibilityChain;
    }

    /**
     * @param AbstractFormatSpecification $specification
     */
    public function addChainBlock(AbstractFormatSpecification $specification)
    {
        if (!empty($this->responsibilityChain))
        {
            $length = count($this->responsibilityChain)-1;
            $this->responsibilityChain[$length]->setSuccessor($specification);
        }

        $this->responsibilityChain []= $specification;
    }

    /**
     * @param \SplFileObject  $file
     * @return mixed
     */
    public function getReader(\SplFileObject  $file)
    {
        return null;
    }
}