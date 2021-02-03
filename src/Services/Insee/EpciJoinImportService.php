<?php


namespace App\Services\Insee;

use App\Entity\City;
use App\Entity\Epci;
use App\Entity\ImportExport;
use App\Manager\CityManager;
use App\Manager\EpciManager;
use App\Manager\ImportExportManager;
use App\Repository\CityRepository;
use App\Repository\EpciRepository;
use DateTime;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;
use SplFileObject;

/**
 * Class EpciJoinImportService
 * @package App\Services\Insee
 */
class EpciJoinImportService extends AbstractInseeImport
{

    const SIREN_NUMBER_KEY = 4;
//    const TYPE_OF_MEMBER = 40;
//    const SIREN_OF_MEMBER = 41;
    const TYPE_OF_MEMBER = 'Type';
    const SIREN_OF_MEMBER = 'Siren membre';

    const CITY_MEMBER = 'Commune';
    const EPCI_MEMBER = 'Groupement';

    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var CityManager
     */
    private $cityManager;
    /**
     * @var EpciRepository
     */
    private $epciRepository;
    /**
     * @var EpciManager
     */
    private $epciManager;

    /**
     * EpciJoinImportService constructor.
     * @param CityRepository $cityRepository
     * @param CityManager $cityManager
     * @param EpciRepository $epciRepository
     * @param EpciManager $epciManager
     * @param ImportExportManager $importExportManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        CityRepository $cityRepository,
        CityManager $cityManager,
        EpciRepository $epciRepository,
        EpciManager $epciManager,
        ImportExportManager $importExportManager,
        LoggerInterface $logger
    ) {
        parent::__construct($logger, $importExportManager);
        $this->cityRepository = $cityRepository;
        $this->cityManager = $cityManager;
        $this->epciRepository = $epciRepository;
        $this->epciManager = $epciManager;
    }

    /**
     * @param ImportExport $task
     * @param array $parameters
     * @return ImportExport
     * @throws \Exception
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
    public function processData(array $data, array $parameters): bool
    {
        $epci = $this->getEpci($data, $parameters);

        if (!$epci instanceof Epci) {
            return false;
        }

        return $this->joinMember($data, $parameters, $epci);
    }

    /**
     * @param array $data
     * @param array $parameters
     * @return null|object
     */
    private function getEpci(array $data, array $parameters)
    {
        $keys = $parameters['keys'];
        $epciSiren = $data[$keys[self::SIREN_NUMBER_KEY]];

        return $this->epciRepository->findOneBy(['siren' => $epciSiren, 'year' => $parameters['year']]);
    }

    /**
     * @param array $data
     * @param array $parameters
     * @param Epci $epci
     * @return bool
     */
    private function joinMember(array $data, array $parameters, Epci $epci): bool
    {
        $keys = $parameters['keys'];
//        $sirenMember = $data[$keys[self::SIREN_OF_MEMBER]];
//        $memberType = $data[$keys[self::TYPE_OF_MEMBER]];
        $sirenMember = $data[self::SIREN_OF_MEMBER];
        $memberType = $data[self::TYPE_OF_MEMBER];

        if ($memberType === self::CITY_MEMBER) {
            return $this->joinCity($epci, $sirenMember, $parameters['year']);
        }

        if ($memberType === self::EPCI_MEMBER) {
            return $this->joinEpci($epci, $sirenMember, $parameters['year']);
        }

        return false;
    }

    /**
     * @param Epci $epci
     * @param string $siren
     * @param string $year
     * @return bool
     */
    private function joinCity(Epci $epci, string $siren, string $year): bool
    {
        $member = $this->cityRepository->findOneBy(['siren' => $siren, 'year' => $year]);

        if (!$member instanceof City) {
            return false;
        }

        $member->addEpci($epci);

        $this->cityManager->saveEntity($member);

        return true;
    }

    /**
     * @param Epci $epci
     * @param string $siren
     * @param string $year
     * @return bool
     */
    private function joinEpci(Epci $epci, string $siren, string $year): bool
    {
        $member = $this->epciRepository->findOneBy(['siren' => $siren, 'year' => $year]);

        if (!$member instanceof Epci) {
            return false;
        }

        $epci->addEpci($member);

        $this->epciManager->saveEntity($epci);

        return true;
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
}
