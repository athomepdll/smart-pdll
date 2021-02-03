<?php


namespace App\Services;

use App\Entity\Department;
use App\Entity\ImportLog;
use App\Entity\ImportModel;
use App\Manager\DataLineManager;
use App\Manager\ImportLogManager;
use App\Repository\ImportLogRepository;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ImportLogService
 * @package App\Services
 */
class ImportLogService
{
    /**
     * @var ImportLogManager
     */
    private $importLogManager;
    /**
     * @var ImportLogRepository
     */
    private $importLogRepository;
    /**
     * @var DataLineManager
     */
    private $dataLineManager;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var string
     */
    private $importFilePath;

    /**
     * ImportLogService constructor.
     * @param DataLineManager $dataLineManager
     * @param string $importFilePath
     * @param ImportLogManager $importLogManager
     * @param ImportLogRepository $importLogRepository
     * @param KernelInterface $kernel
     */
    public function __construct(
        DataLineManager $dataLineManager,
        string $importFilePath,
        ImportLogManager $importLogManager,
        ImportLogRepository $importLogRepository,
        KernelInterface $kernel
    ) {
        $this->dataLineManager = $dataLineManager;
        $this->importFilePath = $importFilePath;
        $this->importLogManager = $importLogManager;
        $this->importLogRepository = $importLogRepository;
        $this->kernel = $kernel;
    }

    /**
     * @param string $year
     * @param Department $department
     * @param ImportModel $importModel
     */
    public function removeImportLogs(string $year, Department $department, ImportModel $importModel)
    {
        $importLogs = $this->importLogRepository->findBy([
            'year' => $year,
            'department' => $department,
            'importModel' => $importModel,
            'isDisabled' => false
        ]);

        /** @var ImportLog $importLogObject */
        foreach ($importLogs as $importLogObject) {
            if (!$importLogObject->getDataLines()->isEmpty()) {
                foreach ($importLogObject->getDataLines() as $dataLine) {
                    $this->dataLineManager->removeEntity($dataLine);
                }
            }

            $this->removeImportFile($importLogObject->getFilePath());
            $importLogObject->setIsDisabled(true);
            $this->importLogManager->saveEntity($importLogObject);
        }
    }

    /**
     * @param ImportLog $importLog
     */
    public function removeImportLog(ImportLog $importLog)
    {
        if (!$importLog->getDataLines()->isEmpty()) {
            foreach ($importLog->getDataLines() as $dataLine) {
                $this->dataLineManager->removeEntity($dataLine);
            }
        }

        $this->removeImportFile($importLog->getFilePath());
        $importLog->setIsDisabled(true);
        $this->importLogManager->saveEntity($importLog);
    }

    /**
     * @param string $importFileName
     */
    private function removeImportFile(string $importFileName)
    {
        $filePath = $this->kernel->getProjectDir() . $this->importFilePath . $importFileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
