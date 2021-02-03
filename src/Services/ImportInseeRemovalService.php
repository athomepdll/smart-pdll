<?php


namespace App\Services;

use App\Entity\City;
use App\Manager\CityManager;
use App\Manager\EpciManager;
use App\Manager\InseeManager;
use App\Repository\CityRepository;
use App\Repository\EpciRepository;
use App\Repository\InseeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ImportInseeRemovalService
 * @package App\Services
 */
class ImportInseeRemovalService
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
     * @var InseeRepository
     */
    private $inseeRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * ImportInseeRemovalService constructor.
     * @param CityRepository $cityRepository
     * @param EpciRepository $epciRepository
     * @param EntityManagerInterface $entityManager
     * @param InseeRepository $inseeRepository
     */
    public function __construct(
        CityRepository $cityRepository,
        EpciRepository $epciRepository,
        EntityManagerInterface $entityManager,
        InseeRepository $inseeRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->epciRepository = $epciRepository;
        $this->entityManager = $entityManager;
        $this->inseeRepository = $inseeRepository;
    }

    /**
     * @param int $year
     * @throws \Exception
     */
    public function removeInseeImport(int $year)
    {
        try {
            $this
                ->removeCities($year)
                ->removeInsee($year)
                ->removeEpcis($year);

            $this->entityManager->clear();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param int $year
     * @return $this
     * @throws \Exception
     */
    private function removeCities(int $year)
    {
        $cities = $this->cityRepository->findBy(['year' => $year]);
        try {
            foreach ($cities as $city) {
                $linkedCities = $this->cityRepository->findBy(['actualCity' => $city]);
                /** @var City $linkedCity */
                foreach ($linkedCities as $linkedCity) {
                    $linkedCity->setActualCity(null);
                    $this->entityManager->persist($linkedCity);
                }
                $this->entityManager->remove($city);
            }
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            dump($exception);
            throw $exception;
        }

        return $this;
    }

    /**
     * @param int $year
     * @return $this
     * @throws \Exception
     */
    private function removeEpcis(int $year)
    {
        $epcis = $this->epciRepository->findBy(['year' => $year]);
        try {
//            $this->entityManager->getConnection()->beginTransaction();
            foreach ($epcis as $epci) {
                $this->entityManager->remove($epci);
            }
            $this->entityManager->flush();
//            $this->entityManager->getConnection()->commit();
        } catch (\Exception $exception) {
//            $this->entityManager->getConnection()->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * @param int $year
     * @return $this
     * @throws \Exception
     */
    private function removeInsee(int $year)
    {
        $insees = $this->inseeRepository->findBy(['year' => $year]);
        try {
//            $this->entityManager->getConnection()->beginTransaction();
            foreach ($insees as $insee) {
                $this->entityManager->remove($insee);
            }
//            $this->entityManager->getConnection()->commit();
            $this->entityManager->flush();
        } catch (\Exception $exception) {
//            $this->entityManager->getConnection()->rollBack();
            throw $exception;
        }

        return $this;
    }
}
