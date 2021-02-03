<?php


namespace App\Services\Insee;

use App\Entity\District;
use App\Entity\Epci;
use App\Entity\ImportExport;
use App\Manager\DistrictManager;
use App\Manager\EpciManager;
use App\Manager\ImportExportManager;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\EpciRepository;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class GroupImportService
 * @package App\Services\Insee
 */
class RegionalPerimeterImportService extends AbstractInseeImport
{
    const CHUNK_SIZE = 2048;
    const COLUMNS = ['G', 'H', 'I'];
    const START_ROW = 1;

    const DISTRICT_NAME_KEY = 2;
    const DEPARTMENT_CODE_KEY = 1;
    const DISTRICT_CODE_KEY = 2;
    const SIREN_NUMBER_KEY = 4;
    const GROUP_NAME_KEY = 5;
    const LEGAL_STATUS_KEY = 6;

    protected $startRow = self::START_ROW;
    protected $chunkSize = self::CHUNK_SIZE;

    const ARTIFICIAL_EPCIS = [
        [
            'name' => 'Pays de la Loire',
            'siren' => '234400034',
            'districts' => ['531', '722', '494', '852', '723', '533', '492', '442', '532', '853', '491', '443', '721',
                '493', '445', '851']
        ],
        [
            'name' => 'Loire-Atlantique',
            'siren' => '224400028',
            'districts' => ['445', '442', '443']
        ],
        [
            'name' => 'Maine-et-Loire',
            'siren' => '224900019',
            'districts' => ['492', '494', '491']
        ],
        [
            'name' => 'Mayenne',
            'siren' => '225300011',
            'districts' => ['531', '533', '532']
        ],
        [
            'name' => 'Sarthe',
            'siren' => '227200029',
            'districts' => ['723', '722', '721']
        ],
        [
            'name' => 'Vendée',
            'siren' => '398150961',
            'districts' => ['852', '853', '851']
        ]
    ];

    /** @var Xlsx */
    protected $reader;
    /**
     * @var DistrictRepository
     */
    private $districtRepository;
    /**
     * @var EpciRepository
     */
    private $epciRepository;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var EpciManager
     */
    private $epciManager;
    /**
     * @var DistrictManager
     */
    private $districtManager;
    /**
     * @var KernelInterface
     */
    private $kernel;


    /**
     * GroupImportService constructor.
     * @param LoggerInterface $logger
     * @param DistrictRepository $districtRepository
     * @param EpciRepository $epciRepository
     * @param CityRepository $cityRepository
     * @param EpciManager $epciManager
     * @param DistrictManager $districtManager
     * @param KernelInterface $kernel
     * @param ImportExportManager $importExportManager
     */
    public function __construct(
        LoggerInterface $logger,
        DistrictRepository $districtRepository,
        EpciRepository $epciRepository,
        CityRepository $cityRepository,
        EpciManager $epciManager,
        DistrictManager $districtManager,
        KernelInterface $kernel,
        ImportExportManager $importExportManager
    ) {
        parent::__construct($logger, $importExportManager);
        $this->districtRepository = $districtRepository;
        $this->epciRepository = $epciRepository;
        $this->cityRepository = $cityRepository;
        $this->epciManager = $epciManager;
        $this->districtManager = $districtManager;
        $this->kernel = $kernel;
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
        $splFile = new SplFileObject($filePath);
        $readerCsv = new CsvReader($splFile);
        $readerCsv->setHeaderRowNumber(0);

        return $readerCsv;
    }

    /**
     * @param array $data
     * @param array $parameters
     */
    public function processData(array $data, array $parameters)
    {
        $district = $this->getOrCreateDistrict($data, $parameters['keys']);
        $this->getOrCreateEpci($data, $parameters['keys'], $parameters['year'], $district);
    }

    /**
     * @param array $data
     * @param array $keys
     * @param string $year
     * @param District $district
     * @return Epci|null
     */
    public function getOrCreateEpci(array $data, array $keys, string $year, District $district): ?Epci
    {
        $epciSiren = $data[$keys[self::SIREN_NUMBER_KEY]];

        /** @var Epci|null $exist */
        $exist = $this->epciRepository->findOneBy(['siren' => $epciSiren, 'year' => $year]);

        if ($exist) {
            return $exist;
        }

        $legalStatus = $data[$keys[self::LEGAL_STATUS_KEY]];
        $epci = new Epci();

        $epci->setName($data[$keys[self::GROUP_NAME_KEY]]);
        $epci->setSiren($epciSiren);
        $epci->setLegalStatus($legalStatus);
        $epci->setYear($year);
        $epci->setIsOwnTax(false);
        if (in_array($legalStatus, Epci::OWN_TAX)) {
            $epci->setIsOwnTax(true);
        }

        $epci->addDistrict($district);

        $this->epciManager->saveEntity($epci);

        return $epci;
    }

    /**
     * @param array $data
     * @param array $keys
     * @return District|null
     */
    public function getOrCreateDistrict(array $data, array $keys): ?District
    {
        $districtCode = substr($data[$keys[self::DEPARTMENT_CODE_KEY]], 0, 2) . substr($data[$keys[self::DISTRICT_CODE_KEY]], 0, 1);

        /** @var District|null $exist */
        $exist = $this->districtRepository->findOneBy(['code' => $districtCode]);

        if ($exist) {
            return $exist;
        }

        $district = new District();
        $district->setName(utf8_encode($data[$keys[self::DISTRICT_NAME_KEY]]));
        $district->setCode($districtCode);

        $this->districtManager->saveEntity($district);

        return $district;
    }

    /**
     * @param int $year
     */
    public function createArtificialEpcis(int $year)
    {
        foreach (self::ARTIFICIAL_EPCIS as $artificialEpci) {
            $epci = new Epci();
            $epci->setName($artificialEpci['name']);
            $epci->setSiren($artificialEpci['siren']);
            $epci->setYear($year);
            $epci->setIsOwnTax(false);
            foreach ($artificialEpci['districts'] as $districtCode) {
                /** @var District $district */
                $district = $this->districtRepository->findOneBy(['code' => $districtCode]);

                if ($district) {
                    $epci->addDistrict($district);
                }
            }

            $this->epciManager->saveEntity($epci);
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return array
     * @throws Exception
     */
    public function convertFileToCsv(UploadedFile $uploadedFile)
    {
        $date = new DateTime('now');

        $filePath = $this->kernel->getProjectDir() . '/tmp/imports/';
        $fileName = $date->format('Y-m-d-H:i:s') . '-insee.csv';

        $uploadedFile->move($filePath, $fileName);

        return ['filePath' => $filePath, 'fileName' => $fileName];
    }


}
