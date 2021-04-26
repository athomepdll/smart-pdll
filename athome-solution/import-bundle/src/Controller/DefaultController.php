<?php


namespace AthomeSolution\ImportBundle\Controller;

use AthomeSolution\ImportBundle\Services\ImportService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class DefaultController
 * @package AthomeSolution\ImportBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     *
     */
    public function indexAction()
    {
        $importService = $this->container->get('athome_solution.import_service');
        $uploadedFile = new UploadedFile($this->container->get('kernel')->getRootDir(). '/test.csv', 'test.csv');
        $importService->handle($uploadedFile, 'user');
//        dump($this->container->get('athome_import.format_manager.csv'));
//        dump($this->container->get('athome_import.user'));
//        dump($this->container->get('athome_import.manager'));
//        dump($this->container->get('athome_import.factory_config_manager'));
    }
}