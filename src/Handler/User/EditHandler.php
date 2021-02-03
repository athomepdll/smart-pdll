<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Handler\AbstractHandler;
use Athome\UserBundle\Model\UserManager;
use Symfony\Component\Form\FormInterface;

/**
 * Class EditHandler
 * @package App\Handler\User
 */
class EditHandler extends AbstractHandler
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * EditHandler constructor.
     * @param UserManager $userManager
     */
    public function __construct(
        UserManager $userManager
    ) {
        $this->userManager = $userManager;
    }


    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        return $form->getData() instanceof User;
    }

    /**
     * @param FormInterface $form
     * @return mixed|void
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        /** @var User $user */
        $user = $form->getData();
        if ($user->getPlainPassword()) {
            $this->userManager->updatePassword($user);
        }

        $this->userManager->update($user);
    }
}
