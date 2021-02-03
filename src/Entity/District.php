<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class District
 * @package App\Entity
 */
class District extends Entity
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $code;
    /** @var Collection */
    protected $cities;
    /** @var Department */
    protected $department;
    /** @var Collection */
    protected $epcis;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->epcis = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }

    /**
     * @return Collection
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @param City $city
     */
    public function addCity(City $city): void
    {
        $this->cities->add($city);
    }

    /**
     * @param Collection $cities
     */
    public function setCities(Collection $cities): void
    {
        $this->cities = $cities;
    }

    /**
     * @return Department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
    }

    /**
     * @return Collection
     */
    public function getEpcis(): Collection
    {
        return $this->epcis;
    }

    /**
     * @param Epci $epci
     */
    public function addEpci(Epci $epci): void
    {
        $this->epcis->add($epci);
    }

    /**
     * @param Collection $epcis
     */
    public function setEpcis(Collection $epcis): void
    {
        $this->epcis = $epcis;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
