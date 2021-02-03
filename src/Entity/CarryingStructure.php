<?php


namespace App\Entity;

use App\Entity\Behavior\SirenBehaviour;

/**
 * Class CarryingStructure
 * @package App\Entity
 */
class CarryingStructure extends Entity
{
    use SirenBehaviour;

    const EPCI = 'epci';
    const CITY = 'city';

    /** @var string */
    protected $name;
    /** @var District */
    protected $district;
    /** @var Department */
    protected $department;
    /** @var string */
    protected $type;
    /** @var string */
    protected $year;
    /** @var string */
    protected $maxYear;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
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
}
