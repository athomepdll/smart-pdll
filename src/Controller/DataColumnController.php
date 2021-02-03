<?php


namespace App\Controller;

use App\Entity\Columns\DataColumn;
use App\Form\DataColumnType;
use App\Handler\DataColumn\NewDataColumnHandler;
use App\Handler\DataColumn\EditDataColumnHandler;
use App\Manager\DataColumnManager;
use App\Repository\DataColumnRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataColumnsController
 * @package App\Controller
 *
 * @Rest\RouteResource("DataColumn")
 */
class DataColumnController extends AbstractFOSRestController
{

    /**
     * @var DataColumnRepository
     */
    private $dataColumnRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var DataColumnManager
     */
    private $dataColumnManager;
    /**
     * @var EditDataColumnHandler
     */
    private $editDataColumnHandler;
    /**
     * @var NewDataColumnHandler
     */
    private $newDataColumnHandler;

    /**
     * DataColumnsController constructor.
     * @param NewDataColumnHandler $newDataColumnHandler
     * @param DataColumnManager $dataColumnManager
     * @param DataColumnRepository $dataColumnRepository
     * @param EditDataColumnHandler $editDataColumnHandler
     * @param LoggerInterface $logger
     */
    public function __construct(
        NewDataColumnHandler $newDataColumnHandler,
        DataColumnManager $dataColumnManager,
        DataColumnRepository $dataColumnRepository,
        EditDataColumnHandler $editDataColumnHandler,
        LoggerInterface $logger
    ) {
        $this->editDataColumnHandler = $editDataColumnHandler;
        $this->dataColumnManager = $dataColumnManager;
        $this->dataColumnRepository = $dataColumnRepository;
        $this->logger = $logger;
        $this->newDataColumnHandler = $newDataColumnHandler;
    }

    /**
     * @Rest\QueryParam(name="config", requirements="\d+", nullable=true )
     *
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $httpStatus = 200;
        $dataColumns = [];
        try {
            $dataColumns = $this->dataColumnRepository->getDataColumns($paramFetcher->all());
        } catch (\Exception $exception) {
            $this->logger->critical('API : ' . $exception->getMessage());
            $httpStatus = 500;
        }

        return new JsonResponse(['columns' => $dataColumns], $httpStatus);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postAction(Request $request)
    {
        $httpStatus = 200;
        $id = null;

        $form = $this->createForm(DataColumnType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataColumn = $this->newDataColumnHandler->handle($form);
            $id = $dataColumn->getId();
        }

        return new JsonResponse(['id' => $id], $httpStatus);
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function deleteAction($slug)
    {
        $httpStatus = 200;
        $success = true;

        /** @var DataColumn $dataColumn */
        $dataColumn = $this->dataColumnRepository->find($slug);

        if (!$dataColumn) {
            $httpStatus = 404;
            $success = false;
            return new JsonResponse(['data' => $success], $httpStatus);
        }

        $this->dataColumnManager->removeEntity($dataColumn);

        return new JsonResponse(['data' => $success], $httpStatus);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function patchAction(Request $request, $slug)
    {
        $httpStatus = 200;
        $success = true;

        $dataColumn = $this->dataColumnRepository->find($slug);

        if (!$dataColumn) {
            return new JsonResponse(['success' => false], 404);
        }

        $form = $this->createForm(DataColumnType::class, $dataColumn, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataColumn = $this->editDataColumnHandler->handle($form);
        }

        return new JsonResponse(['success' => $success, 'newColumnId' => $dataColumn->getId()], $httpStatus);
    }
}
