<?php


namespace App\Controller;

use App\Form\CityFilterType;
use App\Repository\CityRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CityController
 * @package App\Controller
 * @Rest\RouteResource("City")
 */
class CityController extends AbstractFOSRestController
{

    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * CityController constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(CityFilterType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $cities = $this->cityRepository->getCities($filters);

        return new JsonResponse(['data' => $cities]);
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getAction($slug)
    {
        $city = $this->cityRepository->getCity($slug);

        return new JsonResponse(['data' => $city]);
    }
}
