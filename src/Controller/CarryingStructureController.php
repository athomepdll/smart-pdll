<?php


namespace App\Controller;

use App\Form\CarryingStructureType;
use App\Repository\CarryingStructureRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CarryingStructureController
 * @package App\Controller
 * @Rest\RouteResource("CarryingStructure")
 */
class CarryingStructureController extends AbstractFOSRestController
{
    /**
     * @var CarryingStructureRepository
     */
    private $carryingStructureRepository;

    /**
     * CarryingStructureController constructor.
     * @param CarryingStructureRepository $carryingStructureRepository
     */
    public function __construct(
        CarryingStructureRepository $carryingStructureRepository
    ) {
        $this->carryingStructureRepository = $carryingStructureRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(CarryingStructureType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }


        $carryingStructures = $this->carryingStructureRepository->cgetAction($filters);

        return new JsonResponse(['data' => $carryingStructures]);
    }
}
