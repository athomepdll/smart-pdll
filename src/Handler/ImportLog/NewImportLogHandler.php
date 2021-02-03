<?php


namespace App\Handler\ImportLog;

use App\Entity\ImportLog;
use App\Entity\ImportMetadata;
use App\Event\NewImportLogEvent;
use App\Exceptions\ImportData\SirenCarrierNotFoundException;
use App\Exceptions\ImportData\SirenRootNotFoundException;
use App\Handler\AbstractHandler;
use App\Manager\ImportLogManager;
use App\Manager\ImportMetadataManager;
use App\Repository\DataLineRepository;
use App\Services\ImportDataService;
use App\Services\ImportLogService;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class NewImportLogHandler
 * @package App\Handler\ImportLog
 */
class NewImportLogHandler extends AbstractHandler
{
    /**
     * @var ImportLogManager
     */
    private $importLogManager;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var ImportMetadataManager
     */
    private $importMetadataManager;
    /**
     * @var ImportDataService
     */
    private $importDataService;
    /**
     * @var DataLineRepository
     */
    private $dataLineRepository;
    /**
     * @var ImportLogService
     */
    private $importLogService;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    /**
     * NewImportLogHandler constructor.
     * @param ContainerInterface $container
     * @param DataLineRepository $dataLineRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param ImportLogManager $importLogManager
     * @param ImportLogService $importLogService
     * @param ImportMetadataManager $importMetadataManager
     * @param KernelInterface $kernel
     */
    public function __construct(
        ContainerInterface $container,
        DataLineRepository $dataLineRepository,
        EventDispatcherInterface $eventDispatcher,
        ImportLogManager $importLogManager,
        ImportLogService $importLogService,
        ImportMetadataManager $importMetadataManager,
        KernelInterface $kernel
    ) {
        $this->dataLineRepository = $dataLineRepository;
        $this->importDataService = $container->get('athome_solution.import_service');
        $this->eventDispatcher = $eventDispatcher;
        $this->importLogManager = $importLogManager;
        $this->importLogService = $importLogService;
        $this->importMetadataManager = $importMetadataManager;
        $this->kernel = $kernel;
    }

    /**
     * @param FormInterface $form
     * @return bool
     * @throws SirenCarrierNotFoundException
     * @throws SirenRootNotFoundException
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        /** @var ImportLog $importLog */
        $importLog = $form->getData();

        $this->removeExistingData($importLog);

        $importLog->setStatus(ImportLog::STATE_NEW);
        $importLog->setIsDisabled(false);

        $splFile = $this->moveImportFile($importLog);

        $this->importLogManager->saveEntity($importLog);

        try {
            $errors = $this->importDataService->handleImport($splFile, $importLog);
            $importLog->setStatus(ImportLog::STATE_SUCCESS);
        } catch (\Exception | SirenCarrierNotFoundException | SirenRootNotFoundException $exception) {
            $importLog->setStatus(ImportLog::STATE_ERROR);
            $this->importLogManager->saveEntity($importLog);
            throw $exception;
        }
        $this->importLogManager->saveEntity($importLog);
        $this->dispatchEvent($importLog);

        return $errors;
    }

    /**
     * @param ImportLog $importLog
     */
    private function dispatchEvent(ImportLog $importLog)
    {
        $event = new NewImportLogEvent($importLog);

        $this->eventDispatcher->dispatch('newImportLogEvent', $event);
    }

    /**
     * @param ImportLog $importLog
     */
    private function removeExistingData(ImportLog $importLog)
    {
        if ($importLog->isReplace()) {
            $this->importLogService->removeImportLogs(
                $importLog->getYear(),
                $importLog->getDepartment(),
                $importLog->getImportModel()
            );
        }
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        $importLog = $form->getData();
        if (!$importLog instanceof ImportLog) {
            return false;
        }

        if (!$importLog->getImportMetadata() instanceof ImportMetadata) {
            return false;
        }

        return true;
    }

    /**
     * @param ImportLog $importLog
     * @return \SplFileObject
     */
    private function moveImportFile(ImportLog &$importLog)
    {
        $uploadedFile = $importLog->uploadedFile;
        $newPath = $this->kernel->getProjectDir() . '/tmp/imports/';
        $importLog->setFilePath($uploadedFile->getClientOriginalName());

        $uploadedFile->move($newPath, $uploadedFile->getClientOriginalName());

        $splFile = new \SplFileObject($newPath . $uploadedFile->getClientOriginalName());

        return $splFile;
    }
}
