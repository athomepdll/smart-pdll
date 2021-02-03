<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DataLine
 * @package App\Entity
 */
class DataLine extends Entity
{
    /** @var ArrayCollection */
    protected $data;
    /** @var ImportLog */
    protected $importLog;
    /** @var City */
    protected $sirenRoot;
    /** @var string */
    protected $sirenCarrier;

    public function __construct()
    {
        $this->data = new ArrayCollection();
    }

    /**
     * @return City
     */
    public function getSirenRoot(): ?City
    {
        return $this->sirenRoot;
    }

    /**
     * @param City $city
     */
    public function setSirenRoot(?City $city): void
    {
        $this->sirenRoot = $city;
    }

    /**
     * @return ImportLog
     */
    public function getImportLog(): ?ImportLog
    {
        return $this->importLog;
    }

    /**
     * @param ImportLog $importLog
     */
    public function setImportLog(?ImportLog $importLog): void
    {
        $this->importLog = $importLog;
    }

    /**
     * @return string
     */
    public function getSirenCarrier(): ?string
    {
        return $this->sirenCarrier;
    }

    /**
     * @param string $sirenCarrier
     */
    public function setSirenCarrier(?string $sirenCarrier): void
    {
        $this->sirenCarrier = $sirenCarrier;
    }
}
