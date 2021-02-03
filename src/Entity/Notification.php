<?php


namespace App\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Class Notification
 * @package App\Entity
 */
class Notification extends Entity
{
    use TimestampableEntity;

    const STATE_NEW = 'new';
    const STATE_READ = 'read';

    /** @var ImportLog */
    protected $importLog;
    /** @var User */
    protected $user;
    /** @var string */
    protected $state;

    /**
     * Notification constructor.
     * @param ImportLog $importLog
     * @param User $user
     * @throws \Exception
     */
    public function __construct(
        ImportLog $importLog,
        User $user
    ) {
        $this->importLog = $importLog;
        $this->user = $user;
        $this->state = self::STATE_NEW;
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return ImportLog
     */
    public function getImportLog(): ?ImportLog
    {
        return $this->importLog;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->state;
    }
}
