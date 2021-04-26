<?php

namespace Athome\UserBundle\Model;

/**
 * Class AbstractUser
 * @package Athome\UserBundle\Model
 */
abstract class User implements UserInterface
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var string */
    protected $plainPassword;

    /** @var string */
    protected $salt;

    /** @var array */
    protected $roles;

    /** @var bool */
    protected $enabled = false;

    /** @var bool */
    protected $locked = false;

    /** @var \DateTime */
    protected $passwordRequestedAt;

    /** @var \DateTime */
    protected $lastLogin;

    /** @var string */
    protected $confirmationToken;

    /**
     * AbstractUser constructor.
     */
    public function __construct()
    {
        $this->roles = [];
        $this->enabled = false;
        $this->locked = false;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @inheritdoc
     */
    public function setPlainPassword(?string $plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        if (empty($this->roles)) {
            return [static::ROLE_DEFAULT];
        }

        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function addRole(string $role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeRole(string $role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasRole(string $role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @inheritdoc
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        return $this->setPlainPassword(null);
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function setConfirmationToken(string $token)
    {
        $this->confirmationToken = $token;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @inheritdoc
     */
    public function resetConfirmationToken()
    {
        $this->confirmationToken = null;
        $this->setPasswordRequestedAt(null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;

        return$this;
    }

    /**
     * @inheritdoc
     */
    public function isLocked()
    {
        return $this->locked;
    }
}
