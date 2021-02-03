<?php


namespace App\Entity;

use App\Entity\Behavior\SirenBehaviour;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Epci
 * @package App\Entity
 */
class Epci extends Entity
{
    const OWN_TAX = [
        'CA',
        'CC',
        'CU',
        'METRO'
    ];

    use SirenBehaviour;

    /** @var string */
    protected $name;
    /** @var Collection */
    protected $cities;
    /** @var Collection */
    protected $districts;
    /** @var Collection */
    protected $epcis;
    /** @var Insee */
    protected $insee;
    /** @var string */
    protected $legalStatus;
    /** @var boolean */
    protected $isOwnTax;
    /** @var int */
    protected $year;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->districts = new ArrayCollection();
        $this->epcis = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
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
    public function setCities(?Collection $cities): void
    {
        $this->cities = $cities;
    }

    /**
     * @return ArrayCollection
     */
    public function getDistricts(): Collection
    {
        return $this->districts;
    }

    /**
     * @param District $district
     */
    public function addDistrict(District $district): void
    {
        $this->districts->add($district);
    }

    /**
     * @param Collection $districts
     */
    public function setDistricts(?Collection $districts): void
    {
        $this->districts = $districts;
    }

    /**
     * @return ArrayCollection
     */
    public function getEpcis(): Collection
    {
        return $this->epcis;
    }

    /**
     * @param Epci $group
     */
    public function addEpci(Epci $group): void
    {
        $this->epcis->add($group);
    }

    /**
     * @param Collection $epcis
     */
    public function setEpcis(?Collection $epcis): void
    {
        $this->epcis = $epcis;
    }

    /**
     * @return Insee
     */
    public function getInsee(): ?Insee
    {
        return $this->insee;
    }

    /**
     * @param Insee $insee
     */
    public function setInsee(?Insee $insee): void
    {
        $this->insee = $insee;
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
    public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    /**
     * @param string $legalStatus
     */
    public function setLegalStatus(?string $legalStatus): void
    {
        $this->legalStatus = $legalStatus;
    }

    /**
     * @return bool
     */
    public function isOwnTax(): bool
    {
        return $this->isOwnTax;
    }

    /**
     * @param bool $isOwnTax
     */
    public function setIsOwnTax(bool $isOwnTax): void
    {
        $this->isOwnTax = $isOwnTax;
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
