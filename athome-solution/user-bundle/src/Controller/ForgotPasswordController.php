<?php

namespace Athome\UserBundle\Controller;

use Athome\UserBundle\Event\PasswordRequestedEvent;
use Athome\UserBundle\Event\PasswordResetEvent;
use Athome\UserBundle\Events;
use Athome\UserBundle\Form\Type\UserEmailType;
use Athome\UserBundle\Form\Type\UserPasswordType;
use Athome\UserBundle\Model\UserInterface;
use Athome\UserBundle\Model\UserManagerInterface;
use Athome\UserBundle\Service\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class ForgotPasswordController
 * @package Athome\UserBundle\Controller
 */
class ForgotPasswordController extends AbstractController
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var MailerInterface */
    protected $mailer;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /**
     * RegisterController constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManagerInterface $userManager
     * @param MailerInterface $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserManagerInterface $userManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function requestAction(
        Request $request
    ) {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $event = $this->processRequest($data, $request);

            if ($event instanceof PasswordRequestedEvent && $event->getResponse() !== null) {
                return $event->getResponse();
            }
        }

        return $this->render('@AthomeUser/security/password_request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function resetAction(
        Request $request,
        string $token
    ) {
        $user = $this->userManager->findUserBy(['confirmationToken' => $token]);

        if ($user instanceof UserInterface === false) {
            $this->addFlash('danger', 'Token not found');

            return $this->render('@AthomeUser/security/password_reset.html.twig');
        }

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->userManager->updatePassword($user);
            $this->userManager->update($user);

            $event = new PasswordResetEvent($user, $request);
            $this->eventDispatcher->dispatch(Events::PASSWORD_RESET_SUCCESSFUL, $event);

            if ($event instanceof PasswordResetEvent && $event->getResponse() !== null) {
                $this->addFlash('success', 'Your password has been updated');

                return $event->getResponse();
            }
        }

        return $this->render('@AthomeUser/security/password_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $data
     * @param Request $request
     *
     * @return PasswordRequestedEvent|null
     */
    private function processRequest(array $data, Request $request)
    {
        $user = $this->userManager->findUserBy(['email' => $data['email']]);

        if ($user instanceof UserInterface === false) {
            $this->addFlash('danger', 'Email not found');

            return null;
        }

        if ($this->userManager->sendResettingPasswordEmail($user) === false) {
            $this->addFlash('danger', 'An error occurred');

            return null;
        }

        $event = new PasswordRequestedEvent($user, $request);
        $this->eventDispatcher->dispatch(Events::PASSWORD_REQUEST_SUCCESSFUL, $event);

        $this->addFlash('success', 'An email has been sent to your inbox');

        return $event;
    }
}
