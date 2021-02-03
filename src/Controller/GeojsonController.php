<?php


namespace App\Controller;

use App\Services\Place\AbstractPlaceImportService;
use App\Services\Place\CitiesImportService;
use App\Services\Place\DepartmentsImportService;
use App\Services\Place\DistrictsImportService;
use App\Services\Place\EpcisImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class GeojsonController
 * @package App\Controller
 */
class GeojsonController extends AbstractController
{
    const GEOJSON_FILES = [
        'districts' => 'ARRONDISSEMENT_PDL.json',
        'cities' => 'COMMUNE_PDL.json',
        'rivers' => 'COURS_EAU_PDL.json',
        'departments' => 'DEPARTEMENT_PDL.json',
        'epcis' => 'EPCI_PDL.json',
        'ocean' => 'FOND_OCEAN.json',
        'regions' => 'REGION_FRANCE.json'
    ];

    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var CitiesImportService
     */
    private $citiesImportService;
    /**
     * @var DistrictsImportService
     */
    private $districtImportService;
    /**
     * @var EpcisImportService
     */
    private $epcisImportService;
    /**
     * @var DepartmentsImportService
     */
    private $departmentsImportService;

    /**
     * GeojsonController constructor.
     * @param KernelInterface $kernel
     * @param CitiesImportService $citiesImportService
     * @param DepartmentsImportService $departmentsImportService
     * @param DistrictsImportService $districtImportService
     * @param EpcisImportService $epcisImportService
     */
    public function __construct(
        KernelInterface $kernel,
        CitiesImportService $citiesImportService,
        DepartmentsImportService $departmentsImportService,
        DistrictsImportService $districtImportService,
        EpcisImportService $epcisImportService
    ) {
        $this->kernel = $kernel;
        $this->citiesImportService = $citiesImportService;
        $this->departmentsImportService = $departmentsImportService;
        $this->districtImportService = $districtImportService;
        $this->epcisImportService = $epcisImportService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getGeojson(Request $request)
    {
        $json = null;
        $type = $request->query->get('type');

        if (isset(self::GEOJSON_FILES[$type])) {
            $filePath = $this->kernel->getProjectDir() . $this->getParameter('geojson_file_path') . self::GEOJSON_FILES[$type];
            $json = file_get_contents($filePath);
        }

        return new JsonResponse(['data' => $json, 200]);
    }

    /**
     * @return JsonResponse
     */
    public function importCities()
    {
        $filePath = $this->kernel->getProjectDir() . $this->getParameter('geojson_file_path') . self::GEOJSON_FILES['cities'];

        $this->citiesImportService->import($filePath);

        return new JsonResponse(['data' => 'success']);
    }

    /**
     * @return JsonResponse
     */
    public function importDepartments()
    {
        $filePath = $this->kernel->getProjectDir() . $this->getParameter('geojson_file_path') . self::GEOJSON_FILES['departments'];

        $this->departmentsImportService->import($filePath);

        return new JsonResponse(['data' => 'success']);
    }

    /**
     * @return JsonResponse
     */
    public function importDistricts()
    {
        $filePath = $this->kernel->getProjectDir() . $this->getParameter('geojson_file_path') . self::GEOJSON_FILES['districts'];

        $this->districtImportService->import($filePath);

        return new JsonResponse(['data' => 'success']);
    }

    /**
     * @return JsonResponse
     */
    public function importEpcis()
    {
        $filePath = $this->kernel->getProjectDir() . $this->getParameter('geojson_file_path') . self::GEOJSON_FILES['epcis'];

        $this->epcisImportService->import($filePath);

        return new JsonResponse(['data' => 'success']);
    }
}
