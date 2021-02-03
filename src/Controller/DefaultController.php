<?php


namespace App\Controller;

use App\Services\Insee\RegionalPerimeterImportService;
use AthomeSolution\ImportBundle\Services\FormatManager\CsvFormatManager;
use AthomeSolution\ImportBundle\Services\ImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /** @var ImportService */
    private $importService;
    /**
     * @var RegionalPerimeterImportService
     */
    private $groupImportService;

    /**
     * DefaultController constructor.
     * @param ContainerInterface $container
     * @param RegionalPerimeterImportService $groupImportService
     */
    public function __construct(
        ContainerInterface $container,
        RegionalPerimeterImportService $groupImportService
    ) {
        $this->importService = $container->get('athome_solution.import_service');
        $this->groupImportService = $groupImportService;
    }

    /**
     * @param KernelInterface $kernel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(KernelInterface $kernel)
    {
//        $uploadedFile = new UploadedFile($kernel->getProjectDir(). '/src/test.csv', 'test.csv');
//        $this->importService->handle($uploadedFile, 'user');
        return $this->render('map/index.html.twig');
//        $uploadedFile = new UploadedFile($kernel->getProjectDir(). '/src/test.csv', 'test.csv');
//        $uploadedFile = new UploadedFile($kernel->getProjectDir(). '/src/test-insee.xls', 'test-insee.xls');
//        $this->importService->handle($uploadedFile, 'manager');
//        $this->groupImportService->importFile($uploadedFile);
    }
}
