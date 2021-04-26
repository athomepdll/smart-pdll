<?php


namespace Athome\UserBundle\Model;


interface UserManagerInterface
{
    /**
     * @return UserInterface
     */
    public function create();

    /**
     * @return string
     */
    public function getClass();

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function updatePassword(UserInterface $user);

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function update(UserInterface $user);

    /**
     * @param array $criteria
     *
     * @return UserInterface
     */
    public function findUserBy(array $criteria);

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function sendResettingPasswordEmail(UserInterface $user);

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function enableUser(UserInterface $user);

    /**
     * @return string
     */
    public function generatePassword();

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function lockUser(UserInterface $user);

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function sendRegistrationConfirmationEmail(UserInterface $user);
}
