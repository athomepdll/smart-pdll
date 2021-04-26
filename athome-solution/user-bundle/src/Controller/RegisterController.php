<?php

namespace Athome\UserBundle\Controller;

use Athome\UserBundle\Event\RegisterEvent;
use Athome\UserBundle\Events;
use Athome\UserBundle\Form\Type\RegisterType;
use Athome\UserBundle\Model\UserInterface;
use Athome\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package Athome\UserBundle\Controller
 */
class RegisterController extends AbstractController
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
    public function registerAction(
        Request $request
    ) {
        $user = $this->userManager->create();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userManager->updatePassword($user);
            $this->userManager->update($user);

            $event = new RegisterEvent($user, $request);
            $this->eventDispatcher->dispatch(Events::REGISTRATION_SUCCESSFUL, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return $this->render('@AthomeUser/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function confirmAction(
        Request $request,
        string $token
    ) {
        $user = $this->userManager->findUserBy(['confirmationToken' => $token]);

        if ($user instanceof UserInterface === false) {
            $this->addFlash('danger', 'Token not found');

            return $this->render('@AthomeUser/security/register.html.twig');
        }

        $this->userManager->enableUser($user);

        $event = new RegisterEvent($user, $request);
        $this->eventDispatcher->dispatch(Events::REGISTRATION_CONFIRMED, $event);
        $this->addFlash('success', 'Your account has been activated');

        if ($event instanceof RegisterEvent && $event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->render('@AthomeUser/security/registration_confirmed.html.twig');
    }
}
