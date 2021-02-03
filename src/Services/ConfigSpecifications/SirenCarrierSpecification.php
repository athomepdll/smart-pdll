<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\City;
use App\Entity\Columns\SirenCarrierColumn;
use App\Entity\DataLine;
use App\Entity\Epci;
use App\Exceptions\ImportData\SirenCarrierNotFoundException;
use App\Repository\CityRepository;
use App\Repository\EpciRepository;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class SirenSpecification
 * @package App\Services\ConfigSpecifications
 */
class SirenCarrierSpecification extends AbstractConfigSpecification
{
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var EpciRepository
     */
    private $epciRepository;

    /**
     * SirenRootSpecification constructor.
     * @param CityRepository $cityRepository
     * @param EpciRepository $epciRepository
     */
    public function __construct(
        CityRepository $cityRepository,
        EpciRepository $epciRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->epciRepository = $epciRepository;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws SirenCarrierNotFoundException | \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) || !$data['column'] instanceof SirenCarrierColumn) {
            $this->successor->support($data, $objects);
            return false;
        }

        $this->execute($data, $objects);

        $this->successor->support($data, $objects);
        return true;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed|void
     * @throws SirenCarrierNotFoundException
     */
    public function execute(array $data, array &$objects)
    {
        /** @var DataLine $dataLine */
        $dataLine = $objects['dataLine'];
        $siren = $data['value'];
        $year = $dataLine->getImportLog()->getYear();


        $city = $this->cityRepository->findOneBy(['siren' => $siren, 'year' => $year]);
        $epci = $this->epciRepository->findOneBy(['siren' => $siren, 'year' => $year]);

        if (!$city instanceof City && !$epci instanceof Epci) {
            $objects['errors'] []= 'SIREN Structure Porteuse ' . $siren . ' non trouvé pour l\'année ' . $year . ' à la ligne : ' . $data['rowIndex'];
//            throw new SirenCarrierNotFoundException('SIREN Structure Porteuse ' . $siren . ' non trouvé pour l\'année ' . $year . ' à la ligne : ' . $data['rowIndex']);
        }

        $dataLine->setSirenCarrier($siren);
    }
}
