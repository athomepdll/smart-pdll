<?php


namespace App\Entity\Behavior;

/**
 * Trait SirenBehaviour
 * @package App\Entity\Behaviour
 */
trait SirenBehaviour
{
    /** @var string */
    protected $siren;

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
    public function setSiren(?string $siren): void
    {
        $this->siren = $siren;
    }
}
