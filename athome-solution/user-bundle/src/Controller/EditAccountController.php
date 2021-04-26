<?php

namespace Athome\UserBundle\Controller;

use Athome\UserBundle\Event\AccountUpdatedEvent;
use Athome\UserBundle\Event\PasswordChangedEvent;
use Athome\UserBundle\Event\RegisterEvent;
use Athome\UserBundle\Events;
use Athome\UserBundle\Form\Model\EditAccount;
use Athome\UserBundle\Form\Type\ChangePasswordType;
use Athome\UserBundle\Form\Type\EditAccountType;
use Athome\UserBundle\Model\User;
use Athome\UserBundle\Model\UserInterface;
use Athome\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class ChangePasswordController
 * @package Athome\UserBundle\Controller
 */
class EditAccountController extends AbstractController
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * RegisterController constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManagerInterface $userManager
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserManagerInterface $userManager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editAction(
        Request $request
    ) {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $account = new EditAccount();
        $account->email = $user->getEmail();

        $form = $this->createForm(EditAccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditAccount $data */
            $data = $form->getData();

            $user->setEmail($data->email);
            $user->setPlainPassword($data->newPassword);

            $this->userManager->updatePassword($user);
            $this->userManager->update($user);

            $event = new AccountUpdatedEvent($user, $request);
            $this->eventDispatcher->dispatch(Events::ACCOUNT_UPDATE_SUCCESSFUL, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return $this->render('@AthomeUser/security/edit_account.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
