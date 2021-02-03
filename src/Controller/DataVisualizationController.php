<?php


namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataVisualizationController
 * @package App\Controller
 */
class DataVisualizationController extends AbstractController
{

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * DataVisualizationController constructor.
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        NotificationRepository $notificationRepository
    ) {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        /** @var ArrayCollection $notifications */
        $notifications = $this->notificationRepository->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('data/index.html.twig', [
            'newNotifications' => count($notifications),
        ]);
    }
}
