<?php

namespace Athome\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * Interface UserInterface
 * @package Athome\UserBundle\Model
 */
interface UserInterface extends BaseUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return UserInterface
     */
    public function setEmail(string $email);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return null|string
     */
    public function getSalt();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     *
     * @return UserInterface
     */
    public function setPassword(string $password);

    /**
     * @return null|string
     */
    public function getPlainPassword();

    /**
     * @param string $password
     *
     * @return UserInterface
     */
    public function setPlainPassword(?string $password);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return UserInterface
     */
    public function addRole(string $role);

    /**
     * @param string $role
     *
     * @return UserInterface
     */
    public function removeRole(string $role);

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role);

    /**
     * @return null|bool
     */
    public function isEnabled();

    /**
     * @param bool $enabled
     *
     * @return UserInterface
     */
    public function setEnabled(bool $enabled);

    /**
     * @return null|\DateTime
     */
    public function getLastLogin();

    /**
     * @param \DateTime $lastLogin
     *
     * @return UserInterface
     */
    public function setLastLogin(\DateTime $lastLogin);

    /**
     * @return UserInterface
     */
    public function eraseCredentials();

    /**
     * @return string
     */
    public function getConfirmationToken();

    /**
     * @param string $token
     *
     * @return UserInterface
     */
    public function setConfirmationToken(string $token);

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt();

    /**
     * @param \DateTime $passwordRequestedAt
     *
     * @return UserInterface
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt);

    /**
     * @return UserInterface
     */
    public function resetConfirmationToken();

    /**
     * @param bool $locked
     *
     * @return UserInterface
     */
    public function setLocked(bool $locked);

    /**
     * @return bool
     */
    public function isLocked();
}
