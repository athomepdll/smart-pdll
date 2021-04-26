<?php


namespace Athome\UserBundle\Service;


use Athome\UserBundle\Model\UserInterface;

interface MailerInterface
{
    /**
     * @param UserInterface $user
     * @return int
     */
    public function sendResettingPasswordEmail(UserInterface $user);

    /**
     * @param UserInterface $user
     * @return int
     */
    public function sendRegistrationConfirmationEmail(UserInterface $user);
}
