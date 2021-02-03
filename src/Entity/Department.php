<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Department
 * @package App\Entity
 */
class Department extends Entity
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $code;
    /** @var ArrayCollection */
    protected $districts;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }

    public function __construct()
    {
        $this->districts = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getDistricts(): ArrayCollection
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
     * @param ArrayCollection $districts
     */
    public function setDistricts(ArrayCollection $districts): void
    {
        $this->districts = $districts;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
