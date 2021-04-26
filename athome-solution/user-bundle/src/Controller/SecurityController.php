<?php

namespace Athome\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package Athome\UserBundle\Controller
 */
class SecurityController extends AbstractController
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;

    /**
     * SecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils
    ) {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @return Response
     */
    public function loginAction()
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('@AthomeUser/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
