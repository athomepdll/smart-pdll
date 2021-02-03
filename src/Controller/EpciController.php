<?php


namespace App\Controller;

use App\Form\EpciFilterType;
use App\Repository\EpciRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EpciController
 * @package App\Controller
 * @Rest\RouteResource("Epci")
 */
class EpciController extends AbstractFOSRestController
{
    /**
     * @var EpciRepository
     */
    private $epciRepository;

    /**
     * EpciController constructor.
     * @param EpciRepository $epciRepository
     */
    public function __construct(
        EpciRepository $epciRepository
    ) {
        $this->epciRepository = $epciRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(EpciFilterType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $epcis = $this->epciRepository->getEpcis($filters);

        return new JsonResponse(['data' => $epcis]);
    }
}
