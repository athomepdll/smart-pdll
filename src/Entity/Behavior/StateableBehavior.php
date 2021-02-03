<?php

namespace App\Entity\Behavior;

/**
 * Trait StateableBehavior.
 *
 * StateableBehavior is used for entities which are managed through a state machine,
 * please referer to the application configuration for concerned entities.
 *
 * @package App\Entity\Behaviour
 */
trait StateableBehavior
{
    /**
     * The state key identifier.
     *
     * @var string
     */
    protected $state;

    /**
     * {@inheritdoc}
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(string $state)
    {
        $this->state = $state;

        return $this;
    }
}
