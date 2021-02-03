<?php


namespace App\Command;

use App\Entity\ImportExport;
use App\Handler\ImportInsee\ImportInseeHandler;
use App\Repository\ImportExportRepository;
use App\Services\Insee\CitiesImportService;
use App\Services\Insee\EpciJoinImportService;
use App\Services\Insee\InseeSirenImportService;
use App\Services\Insee\NewCitiesImportService;
use App\Services\Insee\RegionalPerimeterImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InseeCommand
 * @package App\Command
 */
class InseeCommand extends Command
{
    protected static $defaultName = 'app:import:insee';
    /**
     * @var ImportExportRepository
     */
    private $importExportRepository;
    /**
     * @var RegionalPerimeterImportService
     */
    private $regionalPerimeterImportService;
    /**
     * @var InseeSirenImportService
     */
    private $inseeSirenImportService;
    /**
     * @var CitiesImportService
     */
    private $citiesImportService;
    /**
     * @var NewCitiesImportService
     */
    private $newCitiesImportService;
    /**
     * @var EpciJoinImportService
     */
    private $epciJoinImportService;

    /**
     * InseeCommand constructor.
     * @param EpciJoinImportService $epciJoinImportService
     * @param ImportExportRepository $importExportRepository
     * @param RegionalPerimeterImportService $regionalPerimeterImportService
     * @param InseeSirenImportService $inseeSirenImportService
     * @param CitiesImportService $citiesImportService
     * @param NewCitiesImportService $newCitiesImportService
     */
    public function __construct(
        EpciJoinImportService $epciJoinImportService,
        ImportExportRepository $importExportRepository,
        RegionalPerimeterImportService $regionalPerimeterImportService,
        InseeSirenImportService $inseeSirenImportService,
        CitiesImportService $citiesImportService,
        NewCitiesImportService $newCitiesImportService
    ) {
        parent::__construct();
        $this->importExportRepository = $importExportRepository;
        $this->regionalPerimeterImportService = $regionalPerimeterImportService;
        $this->inseeSirenImportService = $inseeSirenImportService;
        $this->citiesImportService = $citiesImportService;
        $this->newCitiesImportService = $newCitiesImportService;
        $this->epciJoinImportService = $epciJoinImportService;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $regionalPerimeterTask = $this->importExportRepository->findRunnableTaskByName(ImportInseeHandler::REGIONAL_PERIMETER);

        if ($regionalPerimeterTask instanceof ImportExport) {
            $regionalPerimeterTask->setState(ImportExport::STATE_RUNNING);
            $this->regionalPerimeterImportService->import($regionalPerimeterTask);
            return;
        }

        $inseeSirenTask = $this->importExportRepository->findRunnableTaskByName(ImportInseeHandler::INSEE_SIREN);

        if ($inseeSirenTask instanceof ImportExport) {
            $inseeSirenTask->setState(ImportExport::STATE_RUNNING);
            $task = $this->inseeSirenImportService->import($inseeSirenTask);
            $this->clearFile($task);
            return;
        }

        $citiesTask = $this->importExportRepository->findRunnableTaskByName(ImportInseeHandler::CITIES);

        if ($citiesTask instanceof ImportExport) {
            $citiesTask->setState(ImportExport::STATE_RUNNING);
            $task = $this->citiesImportService->import($citiesTask);
            $this->clearFile($task);
            return;
        }

        $newCitiesTask = $this->importExportRepository->findRunnableTaskByName(ImportInseeHandler::NEW_CITIES);

        if ($newCitiesTask instanceof ImportExport) {
            $newCitiesTask->setState(ImportExport::STATE_RUNNING);
            $task = $this->newCitiesImportService->import($newCitiesTask);
            $this->clearFile($task);
            return;
        }

        $joinEpciCityTask = $this->importExportRepository->findRunnableTaskByName(ImportInseeHandler::INSEE_JOIN);

        if ($joinEpciCityTask instanceof ImportExport) {
            $joinEpciCityTask->setState(ImportExport::STATE_RUNNING);
            $task = $this->epciJoinImportService->import($joinEpciCityTask);
            $this->clearFile($task);
            return;
        }
    }

    /**
     * @param ImportExport $task
     * @return bool
     */
    private function clearFile(ImportExport $task): bool
    {
        if ($task->getState() !== ImportExport::STATE_SUCCESS) {
            return false;
        }

        $fileRealPath = $task->getFilePath() . $task->getFileName();

        if (!file_exists($fileRealPath)) {
            return false;
        }

        unlink($fileRealPath);

        return true;
    }
}
