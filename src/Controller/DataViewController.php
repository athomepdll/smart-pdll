<?php


namespace App\Controller;

use App\Repository\DataViewRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataViewController
 * @package App\Controller
 * @Rest\RouteResource("DataView")
 */
class DataViewController extends AbstractFOSRestController
{
    /**
     * @var DataViewRepository
     */
    private $dataViewRepository;

    /**
     * DataViewController constructor.
     * @param DataViewRepository $dataViewRepository
     */
    public function __construct(
        DataViewRepository $dataViewRepository
    ) {
        $this->dataViewRepository = $dataViewRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $dataViews = $this->dataViewRepository->cgetDataView($request->query->all());

        return new JsonResponse(['data' => $dataViews]);
    }
}
