<?php


namespace App\Entity;

use App\Entity\Behavior\SirenBehaviour;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class City
 * @package App\Entity
 */
class City extends Entity
{
    use SirenBehaviour;
    /** @var string */
    protected $insee;
    /** @var string */
    protected $name;
    /** @var District */
    protected $district;
    /** @var Collection */
    protected $epcis;
    /** @var City */
    protected $actualCity;
    /** @var string */
    protected $year;

    public function __construct()
    {
        $this->epcis = new ArrayCollection();
    }

    /**
     * @return District
     */
    public function getDistrict(): ?District
    {
        return $this->district;
    }

    /**
     * @param District $district
     */
    public function setDistrict(?District $district): void
    {
        $this->district = $district;
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
    public function addEpci(Epci $epci)
    {
        $this->epcis->add($epci);
    }

    /**
     * @param Collection $epcis
     */
    public function setEpcis(?Collection $epcis): void
    {
        $this->epcis = $epcis;
    }

    /**
     * @return City
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(?City $city): void
    {
        $this->city = $city;
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
    public function getInsee(): ?string
    {
        return $this->insee;
    }

    /**
     * @param string $insee
     */
    public function setInsee(?string $insee): void
    {
        $this->insee = $insee;
    }

    /**
     * @return string
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(?string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return City
     */
    public function getActualCity(): ?City
    {
        return $this->actualCity;
    }

    /**
     * @param City $actualCity
     */
    public function setActualCity(?City $actualCity): void
    {
        $this->actualCity = $actualCity;
    }
}
