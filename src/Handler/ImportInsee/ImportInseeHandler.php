<?php


namespace App\Handler\ImportInsee;

use App\Entity\ImportExport;
use App\Factory\ImportExportFactory;
use App\Handler\AbstractHandler;
use App\Manager\ImportExportManager;
use App\Repository\ImportExportRepository;
use App\Services\ImportInseeRemovalService;
use App\Services\Insee\RegionalPerimeterImportService;
use Symfony\Component\Form\FormInterface;

/**
 * Class ImportInseeHandler
 * @package App\Handler
 */
class ImportInseeHandler extends AbstractHandler
{
    const REGIONAL_PERIMETER = 'regional_perimeter';
    const INSEE_JOIN = 'insee_join';
    const INSEE_SIREN = 'insee_siren';
    const CITIES = 'cities';
    const NEW_CITIES = 'new_cities';
    /**
     * @var ImportExportFactory
     */
    private $importExportFactory;
    /**
     * @var ImportExportManager
     */
    private $importExportManager;
    /**
     * @var RegionalPerimeterImportService
     */
    private $regionalPerimeterImportService;
    /**
     * @var ImportInseeRemovalService
     */
    private $importInseeRemovalService;
    /**
     * @var ImportExportRepository
     */
    private $importExportRepository;

    /**
     * ImportInseeHandler constructor.
     * @param ImportExportRepository $importExportRepository
     * @param ImportExportFactory $importExportFactory
     * @param ImportExportManager $importExportManager
     * @param ImportInseeRemovalService $importInseeRemovalService
     * @param RegionalPerimeterImportService $regionalPerimeterImportService
     */
    public function __construct(
        ImportExportRepository $importExportRepository,
        ImportExportFactory $importExportFactory,
        ImportExportManager $importExportManager,
        ImportInseeRemovalService $importInseeRemovalService,
        RegionalPerimeterImportService $regionalPerimeterImportService
    ) {
        $this->importExportFactory = $importExportFactory;
        $this->importExportManager = $importExportManager;
        $this->importInseeRemovalService = $importInseeRemovalService;
        $this->regionalPerimeterImportService = $regionalPerimeterImportService;
        $this->importExportRepository = $importExportRepository;
    }

    /**
     * @param FormInterface $form
     * @throws \Exception
     */
    private function createImportExportTasks(FormInterface $form)
    {
        $data = $form->getData();

        $this->regionalPerimeterImportService->createArtificialEpcis($data['year']);

        $regionalPerimeterTask = $this->importExportFactory->createImportExport(
            ImportExport::INSEE_IMPORT_TYPE,
            $data[self::REGIONAL_PERIMETER],
            self::REGIONAL_PERIMETER,
            $data['year']
        );

        $joinTask = $this->importExportFactory->createFromImportExport($regionalPerimeterTask, self::INSEE_JOIN);

        $inseeSirenTask = $this->importExportFactory->createImportExport(
            ImportExport::INSEE_IMPORT_TYPE,
            $data[self::INSEE_SIREN],
            self::INSEE_SIREN,
            $data['year']
        );

        $citiesTask = $this->importExportFactory->createImportExport(
            ImportExport::INSEE_IMPORT_TYPE,
            $data[self::CITIES],
            self::CITIES,
            $data['year']
        );

        $newCities = $this->importExportFactory->createImportExport(
            ImportExport::INSEE_IMPORT_TYPE,
            $data[self::NEW_CITIES],
            self::NEW_CITIES,
            $data['year']
        );

        $this->importExportManager->saveEntity($regionalPerimeterTask, false);
        $this->importExportManager->saveEntity($joinTask, false);
        $this->importExportManager->saveEntity($inseeSirenTask, false);
        $this->importExportManager->saveEntity($citiesTask, false);
        $this->importExportManager->saveEntity($newCities);

    }

    /**
     * @param FormInterface $form
     * @throws \Exception
     */
    private function removeInseeImport(FormInterface $form)
    {
        $data = $form->getData();
        $year = $data['year'];
        $this->importInseeRemovalService->removeInseeImport($year);
    }

    private function cleanImportExport()
    {
        $tasks = $this->importExportRepository->findBy(['enabled' => true]);
        /** @var ImportExport $task */
        foreach ($tasks as $task) {
            $task->setEnabled(false);
            $this->importExportManager->saveEntity($task);
        }
    }

    /**
     * @param FormInterface $form
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form): bool
    {
        if (!$this->validate($form)) {
            return false;
        }
        try {
            $this->cleanImportExport();
            $this->removeInseeImport($form);
            $this->createImportExportTasks($form);
        } catch (\Exception $exception) {
            throw $exception;
        }

        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        $data = $form->getData();

        if (!isset($data[self::REGIONAL_PERIMETER]) ||
            !isset($data[self::INSEE_SIREN]) ||
            !isset($data[self::CITIES]) ||
            !isset($data[self::NEW_CITIES])
        ) {
            return false;
        }

        return true;
    }
}
