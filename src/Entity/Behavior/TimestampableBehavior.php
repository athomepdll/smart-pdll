<?php


namespace App\Entity\Behavior;

/**
 * Trait TimestampableBehaviour
 * @package App\Entity\Behavior
 */
trait TimestampableBehavior
{
    /** @var \DateTime */
    public $createdAt;

    /** @var \DateTime */
    public $updatedAt;
}
