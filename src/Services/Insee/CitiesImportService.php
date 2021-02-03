<?php


namespace App\Services\Insee;

use App\Entity\City;
use App\Entity\District;
use App\Entity\ImportExport;
use App\Entity\Insee;
use App\Manager\CityManager;
use App\Manager\ImportExportManager;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\InseeRepository;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CitiesImportService
 * @package App\Services\Insee
 */
class CitiesImportService extends AbstractInseeImport
{
    const REGION_INDEX = 'REG';
    const INSEE_INDEX = 'CODGEO';
    const NAME_INDEX = 'LIBGEO';
    const DISTRICT_INDEX = 'ARR';

    /** @var CsvReader */
    protected $reader;

    /**
     * @var CityManager
     */
    private $cityManager;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var DistrictRepository
     */
    private $districtRepository;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var InseeRepository
     */
    private $inseeRepository;

    /**
     * CitiesImportService constructor.
     * @param CityManager $cityManager
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param ImportExportManager $importExportManager
     * @param InseeRepository $inseeRepository
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(
        CityManager $cityManager,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        ImportExportManager $importExportManager,
        InseeRepository $inseeRepository,
        KernelInterface $kernel,
        LoggerInterface $logger
    ) {
        parent::__construct($logger, $importExportManager);
        $this->cityManager = $cityManager;
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->kernel = $kernel;
        $this->inseeRepository = $inseeRepository;
    }

    /**
     * @param ImportExport $task
     * @param array $parameters
     * @return ImportExport
     * @throws Exception
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
     * @return City|null|object
     */
    public function processData(array $data, array $parameters)
    {
        if ($data[self::REGION_INDEX] !== '52') {
            return null;
        }

        $insee = $data[self::INSEE_INDEX];
        $exist = $this->cityRepository->findOneBy(['insee' => $insee, 'year' => $parameters['year']]);

        if ($exist) {
            return $exist;
        }

        $inseeSiren = $this->inseeRepository->findOneBy(['insee' => $insee, 'year' => $parameters['year']]);
        $district = $this->districtRepository->findOneBy(['code' => $data[self::DISTRICT_INDEX]]);

        $city = new City();

        $city->setInsee($insee);
        $city->setName($data[self::NAME_INDEX]);
        $city->setYear($parameters['year']);

        if ($inseeSiren instanceof Insee) {
            $city->setSiren($inseeSiren->getSiren());
        }

        if ($district instanceof District) {
            $city->setDistrict($district);
        }

        $this->cityManager->saveEntity($city);

        return $city;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function convertFileToCsv(UploadedFile $uploadedFile)
    {
//        $this->reader = IOFactory::createReaderForFile($uploadedFile->getRealPath());
//        $spreadsheet = $this->reader->load($uploadedFile->getRealPath());
//        $writer = new Csv($spreadsheet);
//        $writer->setSheetIndex(0);
        $date = new DateTime('now');

        $filePath = $this->kernel->getProjectDir() . '/tmp/imports/';
        $fileName = $date->format('Y-m-d-H:i:s') . '-cities.csv';
        $uploadedFile->move($filePath, $fileName);

        return ['filePath' => $filePath, 'fileName' => $fileName];
    }
}
