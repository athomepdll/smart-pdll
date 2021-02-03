<?php


namespace App\Services\Insee;

use App\Entity\City;
use App\Entity\ImportExport;
use App\Manager\CityManager;
use App\Manager\ImportExportManager;
use App\Repository\CityRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class NewCitiesImportService
 * @package App\Services\Insee
 */
class NewCitiesImportService extends AbstractInseeImport
{
    const NEW_INSEE_INDEX = 'DepComN';
    const OLD_INSEE_INDEX = 'DepComA';

    /**
     * @var CityManager
     */
    private $cityManager;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * NewCitiesImportService constructor.
     * @param CityManager $cityManager
     * @param CityRepository $cityRepository
     * @param EntityManagerInterface $entityManager
     * @param ImportExportManager $importExportManager
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(
        CityManager $cityManager,
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager,
        ImportExportManager $importExportManager,
        KernelInterface $kernel,
        LoggerInterface $logger
    ) {
        parent::__construct($logger, $importExportManager);
        $this->cityManager = $cityManager;
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;
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
     * @param array $data
     * @param array $parameters
     * @return bool
     */
    public function processData(array $data, array $parameters)
    {
        $exist = $this->cityRepository->findOneBy(['insee' => $data[self::NEW_INSEE_INDEX], 'year' => $parameters['year']]);

        if (!$exist instanceof City) {
            return 0;
        }

        $dql = $this->entityManager->createQuery(
            'UPDATE App\Entity\City c SET c.actualCity = :actualCityId
             where (c.insee = :oldInsee or c.actualCity IN (select co.id FROM App\Entity\City co where co.insee = :oldInsee )) and c.id != :actualCityId '
        );
        $dql
            ->setParameter('oldInsee', $data[self::OLD_INSEE_INDEX])
            ->setParameter('actualCityId', $exist->getId());

        return $dql->getResult();
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
     * @param UploadedFile $uploadedFile
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function convertFileToCsv(UploadedFile $uploadedFile)
    {
//        $spreadsheetReader = IOFactory::createReaderForFile($uploadedFile->getRealPath());
//        $spreadsheet = $spreadsheetReader->load($uploadedFile->getRealPath());
//        $writer = new Csv($spreadsheet);
//        $writer->setSheetIndex(0);
        $date = new DateTime('now');

        $filePath = $this->kernel->getProjectDir() . '/tmp/imports/';
        $fileName = $date->format('Y-m-d-H:i:s') . '-new-cities.csv';

        $uploadedFile->move($filePath, $fileName);

        return ['filePath' => $filePath, 'fileName' => $fileName];
    }
}
