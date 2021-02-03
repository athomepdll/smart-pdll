<?php


namespace App\Controller;

use App\Form\DistrictFilterType;
use App\Repository\DistrictRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DistrictController
 * @package App\Controller
 * @Rest\RouteResource("District")
 */
class DistrictController extends AbstractFOSRestController
{

    /**
     * @var DistrictRepository
     */
    private $districtRepository;

    /**
     * DistrictController constructor.
     * @param DistrictRepository $districtRepository
     */
    public function __construct(
        DistrictRepository $districtRepository
    ) {
        $this->districtRepository = $districtRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(DistrictFilterType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $districts = $this->districtRepository->getDistricts($filters);

        return new JsonResponse(['data' => $districts]);
    }
}
