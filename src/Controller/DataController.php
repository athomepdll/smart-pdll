<?php


namespace App\Controller;

use App\Entity\City;
use App\Form\DataType;
use App\Form\DownloadFileType;
use App\Form\TerritoryType;
use App\Handler\Data\DataExportHandler;
use App\Handler\Data\DataHandler;
use App\Handler\Data\DataMapHandler;
use App\Handler\Files\FilesDownloadHandler;
use App\Repository\DataRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

/**
 * Class DataController
 * @package App\Controller
 * @Rest\RouteResource("Data")
 */
class DataController extends AbstractFOSRestController
{
    /**
     * @var DataHandler
     */
    private $dataHandler;
    /**
     * @var DataRepository
     */
    private $dataRepository;
    /**
     * @var DataMapHandler
     */
    private $dataMapHandler;
    /**
     * @var DataExportHandler
     */
    private $dataExportHandler;
    /**
     * @var FilesDownloadHandler
     */
    private $filesDownloadHandler;

    /**
     * DataController constructor.
     * @param DataExportHandler $dataExportHandler
     * @param DataHandler $dataHandler
     * @param DataMapHandler $dataMapHandler
     * @param DataRepository $dataRepository
     * @param FilesDownloadHandler $filesDownloadHandler
     */
    public function __construct(
        DataExportHandler $dataExportHandler,
        DataHandler $dataHandler,
        DataMapHandler $dataMapHandler,
        DataRepository $dataRepository,
        FilesDownloadHandler $filesDownloadHandler
    ) {
        $this->dataExportHandler = $dataExportHandler;
        $this->dataHandler = $dataHandler;
        $this->dataMapHandler = $dataMapHandler;
        $this->dataRepository = $dataRepository;
        $this->filesDownloadHandler = $filesDownloadHandler;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postAction(Request $request)
    {
        $data = [];
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);
        $insee = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $this->dataHandler->handle($form);
            $insee = $form->getData()['city'] instanceof City ?  $form->getData()['city']->getInsee() : null;
        }

        return new JsonResponse(['data' => $data, 'insee' => $insee]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postMapAction(Request $request)
    {
        $data = [];
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $this->dataMapHandler->handle($form);
        }

        return new JsonResponse(['data' => $data]);
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function postExportAction(Request $request)
    {
        $fileName = "";
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $zipFullPath */
            $fileName = $this->dataExportHandler->handle($form);
        }

        return new JsonResponse(['data' => $fileName], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $data = $this->dataRepository->getDataByFilters($request->query->all());


        return new JsonResponse(['data' => $data]);
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function getFileAction(Request $request)
    {
        $form =$this->createForm(DownloadFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('now');
            $fullPath = $this->filesDownloadHandler->handle($form);
            $response = new Response(file_get_contents($fullPath));
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                "{$date->format('Y-m-d')}-Export-Data.ods"
            );
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-length', filesize($fullPath));

            @unlink($fullPath);

            return $response;
        }
    }
}
