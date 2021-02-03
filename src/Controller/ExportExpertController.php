<?php


namespace App\Controller;

use App\Entity\ImportLog;
use App\Form\ExportExpertFilters\ExportExpertsFiltersType;
use App\Handler\ExportExpert\ExportHandler;
use App\Handler\ExportExpert\ListHandler;
use App\Repository\ImportLogRepository;
use App\Repository\NotificationRepository;
use App\Services\DownloadService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;

class ExportExpertController extends AbstractFOSRestController
{
    /**
     * @var ImportLogRepository
     */
    protected $importLogsRepository;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var DownloadService
     */
    protected $downloadService;
    /**
     * @var ListHandler
     */
    private $listHandler;
    /**
     * @var ExportHandler
     */
    private $exportHandler;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * ExportExpertController constructor.
     * @param DownloadService $downloadService
     * @param ExportHandler $exportHandler
     * @param ImportLogRepository $importLogRepository
     * @param NotificationRepository $notificationRepository
     * @param KernelInterface $kernel
     * @param ListHandler $listHandler
     */
    public function __construct(
        DownloadService $downloadService,
        ExportHandler $exportHandler,
        ImportLogRepository $importLogRepository,
        KernelInterface $kernel,
        ListHandler $listHandler,
        NotificationRepository $notificationRepository
    ) {
        $this->downloadService = $downloadService;
        $this->exportHandler = $exportHandler;
        $this->importLogsRepository = $importLogRepository;
        $this->kernel = $kernel;
        $this->listHandler = $listHandler;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('view', 'export_expert');
        $notifications = $this->notificationRepository->findBy(['user' => $this->getUser()->getId()]);
        $filtersForm = $this->createForm(ExportExpertsFiltersType::class);
        $filtersForm->handleRequest($request);

        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
            if ($filtersForm->get('submit')->isClicked()) {
                $importLogs = $this->listHandler->handle($filtersForm);
                return $this->render('exportExpert/index.html.twig', [
                    'form' => $filtersForm->createView(),
                    'importLogs' => $importLogs,
                    'newNotifications' => count($notifications),
                ]);
            }

            if ($filtersForm->get('export')->isClicked()) {
                return $this->exportHandler->handle($filtersForm);
            }
        }

        return $this->render('exportExpert/index.html.twig', [
            'form' => $filtersForm->createView(),
            'importLogs' => [],
            'newNotifications' => count($notifications),
        ]);
    }


    /**
     * @param Request   $request
     * @param ImportLog $importLog
     * @return BinaryFileResponse
     */
    public function download(Request $request, ImportLog $importLog)
    {
        $this->denyAccessUnlessGranted('view', 'export_expert');

        $parameter = $this->getParameter('import_file_path');
        return $this->downloadService->download($request, $parameter, null, $importLog);
    }
}
