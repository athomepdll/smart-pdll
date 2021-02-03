<?php


namespace App\Entity;

use Jsor\Doctrine\PostGIS\Functions\Geometry;

/**
 * Class Place
 * @package App\Entity
 */
class Place extends Entity
{
    /** @var string */
    protected $code;

    /** @var Geometry */
    protected $polygons;

    /**
     * @return Geometry
     */
    public function getPolygons(): ?Geometry
    {
        return $this->polygons;
    }

    /**
     * @param string $polygons
     */
    public function setPolygons(?string $polygons): void
    {
        $this->polygons = $polygons;
    }

    /**
     * @param string $code
     * @return Place
     */
    public function setCode(string $code): Place
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
}
