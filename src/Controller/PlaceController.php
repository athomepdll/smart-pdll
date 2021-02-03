<?php


namespace App\Controller;

use App\Form\PlaceType;
use App\Handler\Place\PlaceHandler;
use App\Repository\PlaceRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PlaceController
 * @package App\Controller
 *
 * @Rest\RouteResource("Place")
 */
class PlaceController extends AbstractFOSRestController
{
    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * PlaceController constructor.
     * @param PlaceRepository $placeRepository
     */
    public function __construct(
        PlaceRepository $placeRepository
    ) {
        $this->placeRepository = $placeRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(PlaceType::class);
        $form->handleRequest($request);

        $filters = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $place = $this->placeRepository->getCentroidPlace($filters);

        return new JsonResponse(['data' => $place], 200);
    }
}
