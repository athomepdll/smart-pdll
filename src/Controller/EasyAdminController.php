<?php


namespace App\Controller;

use App\Entity\Columns\DataColumn;

use App\Entity\Enumeration;
use App\Entity\ImportLog;
use App\Entity\ImportModel;
use App\Entity\User;
use App\Exceptions\ImportData\SirenCarrierNotFoundException;
use App\Exceptions\ImportData\SirenRootNotFoundException;
use App\Form\ImportLogType;
use App\Form\ImportModelType;
use App\Form\RegionalPerimeterExportType;
use App\Form\RegionalPerimeterImportType;
use App\Handler\ExportInsee\ExportInseeHandler;
use App\Handler\ImportInsee\ImportInseeHandler;
use App\Handler\ImportLog\NewImportLogHandler;
use App\Handler\ImportModel\CreateImportModelHandler;
use App\Handler\ImportModel\DeleteImportModelHandler;
use App\Handler\ImportModel\EditImportModelHandler;
use App\Repository\CityRepository;
use App\Repository\EnumerationRepository;
use App\Services\DownloadService;
use App\Services\ImportLogService;
use App\Services\ImportModelService;
use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Manager\ConfigManager;
use AthomeSolution\ImportBundle\Repository\ConfigRepository;
use DateTime;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InseeImportController
 * @package App\Controller
 */
class EasyAdminController extends BaseController
{
    const CANCEL_IMPORT_MODEL_ACTION = 'cancel_import_model';
    const EXPORT_INSEE_DATA = 'export_regional_perimeter';
    const IMPORT_DATA_ACTION = 'import_data';
    const IMPORT_MODEL = 'ImportModel';
    const REGIONAL_PERIMETER = 'regional_perimeter';
    const HELP_MARKDOWN = 'help_markdown';
    /**
     * @var ImportInseeHandler
     */
    private $importInseeHandler;
    /**
     * @var ImportModelService
     */
    private $importModelService;
    /**
     * @var CreateImportModelHandler
     */
    private $createImportModelHandler;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var DeleteImportModelHandler
     */
    private $deleteImportModelHandler;
    /**
     * @var EditImportModelHandler
     */
    private $editImportModelHandler;
    /**
     * @var NewImportLogHandler
     */
    private $newImportLogHandler;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var ImportLogService
     */
    private $importLogService;
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var DownloadService
     */
    private $downloadService;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var ExportInseeHandler
     */
    private $exportInseeHandler;
    /**
     * @var EnumerationRepository
     */
    private $enumerationRepository;

    /**
     * EasyAdminController constructor.
     * @param CityRepository $cityRepository
     * @param ConfigManager $configManager
     * @param ConfigRepository $configRepository
     * @param CreateImportModelHandler $createImportModelHandler
     * @param DeleteImportModelHandler $deleteImportModelHandler
     * @param EditImportModelHandler $editImportModelHandler
     * @param EnumerationRepository $enumerationRepository
     * @param ExportInseeHandler $exportInseeHandler
     * @param ImportInseeHandler $importInseeHandler
     * @param ImportLogService $importLogService
     * @param ImportModelService $importModelService
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     * @param NewImportLogHandler $newImportLogHandler
     */
    public function __construct(
        CityRepository $cityRepository,
        ConfigManager $configManager,
        ConfigRepository $configRepository,
        CreateImportModelHandler $createImportModelHandler,
        DeleteImportModelHandler $deleteImportModelHandler,
        EditImportModelHandler $editImportModelHandler,
        EnumerationRepository $enumerationRepository,
        ExportInseeHandler $exportInseeHandler,
        ImportInseeHandler $importInseeHandler,
        ImportLogService $importLogService,
        ImportModelService $importModelService,
        KernelInterface $kernel,
        LoggerInterface $logger,
        NewImportLogHandler $newImportLogHandler
    ) {
        $this->cityRepository = $cityRepository;
        $this->configManager = $configManager;
        $this->configRepository = $configRepository;
        $this->createImportModelHandler = $createImportModelHandler;
        $this->deleteImportModelHandler = $deleteImportModelHandler;
        $this->editImportModelHandler = $editImportModelHandler;
        $this->enumerationRepository = $enumerationRepository;
        $this->exportInseeHandler = $exportInseeHandler;
        $this->importInseeHandler = $importInseeHandler;
        $this->importLogService = $importLogService;
        $this->importModelService = $importModelService;
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->newImportLogHandler = $newImportLogHandler;
        $this->downloadService = new DownloadService($kernel);
    }

    /**
     * @Route("/", name="easyadmin")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @throws ForbiddenActionException
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('admin', 'admin_panel');

        if ($request->query->get('action') === self::REGIONAL_PERIMETER) {
            return $this->importRegionalPerimeter($request);
        }

        if ($request->query->get('action') === self::CANCEL_IMPORT_MODEL_ACTION) {
            return $this->cancelCreateImportModel($request);
        }

        if ($request->query->get('action') === self::IMPORT_DATA_ACTION) {
            return $this->importData($request);
        }

        if ($request->query->get('action') === self::EXPORT_INSEE_DATA) {
            return $this->exportInseeData($request);
        }

        if ($request->query->get('action') === self::HELP_MARKDOWN) {
            return $this->helpMarkdown($request);
        }

        return parent::indexAction($request);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function importRegionalPerimeter(Request $request)
    {
        $form = $this->createForm(RegionalPerimeterImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->importInseeHandler->handle($form);
            } catch (ConstraintViolationException | UniqueConstraintViolationException | ORMException $exception) {
                $this->addFlash('error', 'Un import de données existant empêche un nouvel import INSEE');
                $this->logger->error($exception->getMessage());
            } catch (\Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
                $this->logger->critical($exception->getMessage());
            }
        }

        return $this->render('admin/insee_import/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelCreateImportModel(Request $request)
    {
        $configId = $request->query->get('config');

        if ($configId) {
            /** @var Config $config */
            $config = $this->configRepository->find($configId);
            if ($config) {
                $this->configManager->removeEntity($config);
            }
        }

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'ImportModel'
        ]);
    }

    /**
     * @return Response
     */
    public function editImportModelAction()
    {
        /** @var ImportModel $importModel */
        $importModel = $this->em->getRepository(ImportModel::class)->find($this->request->query->get('id'));

        $form = $this->createForm(ImportModelType::class, $importModel);
        $form->handleRequest($this->request);

//        if (!$this->editImportModelHandler->validate($form)) {
//            $this->addFlash('error', 'Modifications impossible une importation a déjà été effectué avec ce modèle');
//            return $this->redirectToRoute('easyadmin', [
//                'entity' => 'ImportModel',
//            ]);
//        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editImportModelHandler->handle($form);

            $this->addFlash('success', "Modifications appliquées");

            return $this->redirectToRoute('easyadmin', [
                'entity' => 'ImportModel',
            ]);
        }

        return $this->render('admin/import_model/edit.html.twig', [
            'form' => $form->createView(),
            'config' => $importModel->getConfig()->getId(),
            'isEditable' => !$this->editImportModelHandler->isEditable($form) ? 0 : 1,
        ]);
    }

    /**
     * @return Response
     */
    public function newImportModelAction()
    {
        $importModel = new ImportModel();
        if ($this->request->getMethod() !== 'POST') {
            $importModel = $this->importModelService->generateImportModel();
        }

        /** @var DataColumn $importModel */
        $form = $this->createForm(ImportModelType::class, $importModel);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createImportModelHandler->handle($form);

            $this->addFlash('success', 'Création réussie');
            return $this->redirectToRoute('easyadmin', [
                'entity' => 'ImportModel',
            ]);
        }

        return $this->render('admin/import_model/new.html.twig', [
            'form' => $form->createView(),
            'config' => $importModel->getConfig()->id,
            'entity' => 'ImportModel'
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function deleteImportModelAction()
    {
        $id = $this->request->query->get('id');
        $importModel = $this->em->getRepository(ImportModel::class)->find($id);

        $response = $this->redirectToRoute('easyadmin', [
            'entity' => 'ImportModel',
        ]);

        $form = $this->createForm(ImportModelType::class, $importModel);
        $result = $this->deleteImportModelHandler->handle($form);

        if (!$result) {
            $this->addFlash('error', 'Suppression impossible une importation a déjà été effectué avec ce modèle');
            return $response;
        }

        $this->addFlash('success', 'Suppression réussie');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function importData(Request $request)
    {
        /** @var User $userName */
        $userName = $this->getUser()->getEmail();
        $form = $this->createForm(ImportLogType::class, null, ['user' => $userName ?? '']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $errors = $this->newImportLogHandler->handle($form);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error);
                    }
                    return $this->render('admin/import_log/index.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                $this->addFlash('success', 'Importation des données réussie');
            } catch (SirenRootNotFoundException | SirenCarrierNotFoundException $exception) {
                $this->addFlash('error', $exception->getMessage());
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
                $this->logger->critical($exception->getMessage());
            }
        }

        return $this->render('admin/import_log/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function downloadAction()
    {
        $parameter = $this->getParameter('import_file_path');
        return $this->downloadService->download($this->request, $parameter, $this->em);
    }

    /**
     * @return RedirectResponse
     */
    public function deleteLogAction()
    {
        $idImportLog = $this->request->query->get('id');
        $importLog = $this->em->getRepository(ImportLog::class)->find($idImportLog);

        $this->importLogService->removeImportLog($importLog);

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'ImportLog',
            'action' => 'list'
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function exportInseeData(Request $request)
    {
        $years = $this->cityRepository->getYears();
        $form = $this->createForm(RegionalPerimeterExportType::class, null, ['years' => $years]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileContent = $this->exportInseeHandler->handle($form);
            $date = new DateTime('now');
            $response = new Response();
            $response->setContent($fileContent);
            $response->headers->add([
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=Export-INSEE-' . $date->format('Y-d-m') . '-' . $form->getData()['year'] . '.csv',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

            return $response;
        }

        return $this->render('admin/insee_export/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function helpMarkdown(Request $request)
    {
        /** @var Enumeration $enumeration */
        $enumeration = $this->enumerationRepository->findOneBy(['name' => Enumeration::HELP_PAGE]);
        $markdown = $enumeration instanceof Enumeration ? $enumeration->getValue() : '';

        return $this->render('admin/help/edit.html.twig', [
            'markdown' => $markdown
        ]);
    }
}
