<?php


namespace App\Entity;

/**
 * Class Insee
 * @package App\Entity
 */
class Insee extends Entity
{
    /** @var string */
    protected $siren;
    /** @var string */
    protected $insee;
    /** @var int */
    protected $year;

    /**
     * @return string
     */
    public function getSiren(): string
    {
        return $this->siren;
    }

    /**
     * @param string $siren
     */
    public function setSiren(string $siren): void
    {
        $this->siren = $siren;
    }

    /**
     * @return string
     */
    public function getInsee(): string
    {
        return $this->insee;
    }

    /**
     * @param string $insee
     */
    public function setInsee(string $insee): void
    {
        $this->insee = $insee;
    }

    /**
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }
}
