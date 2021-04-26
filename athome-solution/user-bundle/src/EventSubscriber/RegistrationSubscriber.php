<?php


namespace Athome\UserBundle\EventSubscriber;


use Athome\UserBundle\Event\RegisterEvent;
use Athome\UserBundle\Events;
use Athome\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationSubscriber implements EventSubscriberInterface
{
    /** @var bool */
    protected $enableOnRegistration;

    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * RegistrationSubscriber constructor.
     * @param UserManagerInterface $userManager
     * @param bool $enableOnRegistration
     */
    public function __construct(
        UserManagerInterface $userManager,
        bool $enableOnRegistration
    ) {
        $this->enableOnRegistration = $enableOnRegistration;
        $this->userManager = $userManager;
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
            Events::REGISTRATION_SUCCESSFUL => 'onRegistrationSuccessful',
        ];
    }

    /**
     * @param RegisterEvent $event
     */
    public function onRegistrationSuccessful(RegisterEvent $event)
    {
        $user = $event->getUser();

        if ($this->enableOnRegistration === true) {
            $this->userManager->enableUser($user);

            return;
        }

        $this->userManager->sendRegistrationConfirmationEmail($user);
    }
}