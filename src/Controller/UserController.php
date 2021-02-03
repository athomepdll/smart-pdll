<?php


namespace App\Controller;

use App\Entity\Enumeration;
use App\Entity\ImportType;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\UserType;
use App\Handler\User\EditHandler;
use App\Manager\NotificationManager;
use App\Repository\EnumerationRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @var EditHandler
     */
    private $editHandler;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var NotificationManager
     */
    private $notificationManager;
    /**
     * @var EnumerationRepository
     */
    private $enumerationRepository;

    /**
     * UserController constructor.
     * @param EditHandler $editHandler
     * @param EnumerationRepository $enumerationRepository
     * @param NotificationManager $notificationManager
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        EditHandler $editHandler,
        EnumerationRepository $enumerationRepository,
        NotificationManager $notificationManager,
        NotificationRepository $notificationRepository
    ) {
        $this->editHandler = $editHandler;
        $this->enumerationRepository = $enumerationRepository;
        $this->notificationManager = $notificationManager;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @return JsonResponse
     */
    public function checkSession()
    {
        return new JsonResponse('ok', 200);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function read(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('view', $user);
        $notifications = $this->notificationRepository->findBy(['user' => $user->getId()]);
        return $this->render('user/read.html.twig', [
            'user' => $user,
            'newNotifications' => count($notifications),
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('view', $user);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editHandler->handle($form);

            return $this->redirectToRoute('user_read', [
                'user' => $user->getId()
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function preferencesAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return new JsonResponse([
            'department' => $user->department ? $user->department->getId() : null,
            'district' => $user->district ? $user->district->getId() : null,
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function notifications(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('view', $user);

        $notifications = $this->notificationRepository->findBy(['user' => $user, 'state' => Notification::STATE_NEW]);

        return $this->render('user/notifications.html.twig', [
            'user' => $user,
            'notifications' => $notifications
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function consumeNotification(Request $request, User $user, Notification $notification)
    {
        $this->denyAccessUnlessGranted('view', $user);
        $department = $notification->getImportLog()->getDepartment()->getId();
        $year = $notification->getImportLog()->getYear();
        $importModel = $notification->getImportLog()->getImportModel()->getId();
        $importModelName = $notification->getImportLog()->getImportModel()->getImportType()->getName() === ImportType::FINANCIAL
            ? 'importModelFinancial' : 'importModelIndicator';
        $this->notificationManager->removeEntity($notification);

        return $this->redirectToRoute('data_visualization', [
            'department' => $department,
            'yearStart' => $year,
            $importModelName => $importModel
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function help(Request $request)
    {
        $helpMarkdown = $this->enumerationRepository->findOneBy(['name' => Enumeration::HELP_PAGE]);

        return $this->render('help.html.twig', [
            'help' => $helpMarkdown
        ]);
    }
}
