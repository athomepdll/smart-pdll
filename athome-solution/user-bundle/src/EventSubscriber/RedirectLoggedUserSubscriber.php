<?php


namespace Athome\UserBundle\EventSubscriber;

use Athome\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RedirectLoggedUserSubscriber
 * @package Athome\UserBundle\EventSubscriber
 */
class RedirectLoggedUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RedirectLoggedUserSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RouterInterface $router
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() && $this->isUserLogged()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');

            $isAuthenticatedUserOnAnonymousPage = $this->isAuthenticatedUserOnAnonymousPage($currentRoute);

            if ($isAuthenticatedUserOnAnonymousPage) {
                $response =  new RedirectResponse('/');
                $event->setResponse($response);
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserLogged()
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface;
    }

    /**
     * @param string $currentRoute
     *
     * @return bool
     */
    private function isAuthenticatedUserOnAnonymousPage(string $currentRoute)
    {
        return in_array($currentRoute, [
            'user_bundle_security_login',
            'user_bundle_security_register',
            'user_bundle_security_password_request',
            'user_bundle_security_password_reset'
        ]);
    }
}