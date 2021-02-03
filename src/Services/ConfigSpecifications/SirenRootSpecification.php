<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\City;
use App\Entity\Columns\SirenRootColumn;
use App\Entity\Epci;
use App\Exceptions\ImportData\SirenRootNotFoundException;
use App\Repository\CityRepository;
use App\Repository\EpciRepository;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class SirenSpecification
 * @package App\Services\ConfigSpecifications
 */
class SirenRootSpecification extends AbstractConfigSpecification
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * SirenRootSpecification constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws SirenRootNotFoundException | \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) || !$data['column'] instanceof SirenRootColumn) {
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
     * @throws SirenRootNotFoundException
     */
    public function execute(array $data, array &$objects)
    {
        $dataLine = $objects['dataLine'];
        $siren = $data['value'];
        $year = $dataLine->getImportLog()->getYear();

        /** @var City|null $city */
        $city = $this->cityRepository->findOneBy([
            'siren' => $siren,
            'year' => $year
        ]);

        if (!$city instanceof City) {
            $message = 'SIREN implantation ' . $siren . ' non trouvé pour l\'année ' . $year . ' à la ligne : ' . $data['rowIndex'];
            $objects['errors'] []= $message;
//            throw new SirenRootNotFoundException($message);
            return false;
        }

        if ($city->getDistrict() === null || $city->getDistrict()->getDepartment() === null ||
            $city->getDistrict()->getDepartment()->getId() !== $objects['department']) {
            $message = 'Attention SIREN implantation '
                . $siren
                . ' non présent dans le département de l\'import en cours pour l\'année '
                . $year . ' à la ligne : '
                . $data['rowIndex'];
            $objects['errors'] []= $message;

//            throw new SirenRootNotFoundException($message);
        }

        $dataLine->setSirenRoot($city);
    }
}
