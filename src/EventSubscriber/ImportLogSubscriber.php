<?php


namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Entity\User;
use App\Event\NewImportLogEvent;
use App\Manager\NotificationManager;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ImportLogSubscriber
 * @package App\EventSubscriber
 */
class ImportLogSubscriber implements EventSubscriberInterface
{

    /**
     * @var NotificationManager
     */
    private $notificationManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ImportLogSubscriber constructor.
     * @param NotificationManager $notificationManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        NotificationManager $notificationManager,
        UserRepository $userRepository
    ) {
        $this->notificationManager = $notificationManager;
        $this->userRepository = $userRepository;
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
            'newImportLogEvent' => 'onNewImportLog',
        ];
    }

    /**
     * @param NewImportLogEvent $importLogEvent
     * @throws \Exception
     */
    public function onNewImportLog(NewImportLogEvent $importLogEvent)
    {
        $importLog = $importLogEvent->importLog;
        $usersToNotify = $this->userRepository->getByPreferencesOrNull($importLog->getDepartment());

        /** @var User $user */
        foreach ($usersToNotify as $user) {
            $notification = new Notification($importLog, $user);
            $this->notificationManager->saveEntity($notification);
        }
    }
}
