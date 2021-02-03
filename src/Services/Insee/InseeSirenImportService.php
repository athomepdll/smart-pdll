<?php


namespace App\Services\Insee;

use App\Entity\ImportExport;
use App\Entity\Insee;
use App\Manager\ImportExportManager;
use App\Manager\InseeManager;
use App\Repository\InseeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class InseeSirenImportService
 * @package App\Services\Insee
 */
class InseeSirenImportService extends AbstractInseeImport
{

    const REGION_INDEX = 'reg_com';
    const SIREN_INDEX = 'siren';
    const INSEE_INDEX = 'insee';

    /** @var Xls */
    protected $reader;
    /**
     * @var InseeRepository
     */
    private $inseeRepository;
    /**
     * @var InseeManager
     */
    private $inseeManager;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * InseeSirenImportService constructor.
     * @param EntityManagerInterface $entityManager
     * @param InseeRepository $inseeRepository
     * @param InseeManager $inseeManager
     * @param ImportExportManager $importExportManager
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ImportExportManager $importExportManager,
        InseeManager $inseeManager,
        InseeRepository $inseeRepository,
        KernelInterface $kernel,
        LoggerInterface $logger
    ) {
        parent::__construct($logger, $importExportManager);
        $this->entityManager = $entityManager;
        $this->inseeManager = $inseeManager;
        $this->inseeRepository = $inseeRepository;
        $this->kernel = $kernel;
    }

    /**
     * @param ImportExport $task
     * @param array $parameters
     * @return ImportExport
     */
    public function import(ImportExport $task, array $parameters = []): ImportExport
    {
        $parameters['year'] = $task->getYear();
        return parent::import($task, $parameters);
    }

    /**
     * @param string $filePath
     * @return CsvReader
     */
    protected function getReader(string $filePath): CsvReader
    {
        $splFileObject = new SplFileObject($filePath);
        $readerCsv = new CsvReader($splFileObject);
        $readerCsv->setHeaderRowNumber(0);

        return $readerCsv;
    }

    /**
     * @param array $data
     * @param array $parameters
     * @return Insee|null
     */
    public function processData(array $data, array $parameters)
    {
        if ($data[self::REGION_INDEX] !== '52') {
            return null;
        }

        $siren = $data[self::SIREN_INDEX];
        /** @var Insee|null $exist */
        $exist = $this->inseeRepository->findOneBy(['siren' => $siren, 'year' => $parameters['year']]);

        if ($exist) {
            return $exist;
        }

        $insee = new Insee();
        $insee->setSiren($siren);
        $insee->setInsee($data[self::INSEE_INDEX]);
        $insee->setYear($parameters['year']);

        $this->inseeManager->saveEntity($insee);

        return $insee;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Exception
     */
    public function convertFileToCsv(UploadedFile $uploadedFile)
    {
//        $this->reader = IOFactory::createReaderForFile($uploadedFile->getRealPath());
//        $spreadsheet = $this->reader->load($uploadedFile->getRealPath());
//        $writer = new Csv($spreadsheet);
//        $writer->setSheetIndex(0);
        $date = new DateTime('now');

        $filePath = $this->kernel->getProjectDir() . '/tmp/imports/';
        $fileName = $date->format('Y-m-d-H:i:s') . '-insee-siren.csv';
        $uploadedFile->move($filePath, $fileName);

        return ['filePath' => $filePath, 'fileName' => $fileName];
    }
}
