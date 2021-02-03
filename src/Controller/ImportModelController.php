<?php


namespace App\Controller;

use App\Form\ImportModelFilterType;
use App\Repository\ImportModelRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportModelController
 * @package App\Controller
 *
 * @Rest\RouteResource("ImportModel")
 */
class ImportModelController extends AbstractFOSRestController
{
    /**
     * @var ImportModelRepository
     */
    private $importModelRepository;

    /**
     * ImportModelController constructor.
     * @param ImportModelRepository $importModelRepository
     */
    public function __construct(
        ImportModelRepository $importModelRepository
    ) {
        $this->importModelRepository = $importModelRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $form = $this->createForm(ImportModelFilterType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $importModels = $this->importModelRepository->getImportModels($filters);

        return new JsonResponse(['data' => $importModels]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetDomainsAction(Request $request)
    {
        $form = $this->createForm(ImportModelFilterType::class);
        $form->handleRequest($request);

        $filters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }

        $importModels = $this->importModelRepository->getImportModelsSortedByDomains($filters);

        return new JsonResponse(['data' => $importModels]);
    }
}
