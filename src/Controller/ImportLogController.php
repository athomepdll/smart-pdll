<?php


namespace App\Controller;

use App\Repository\ImportLogRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class ImportLogController
 * @package App\Controller
 *
 * @Rest\RouteResource("ImportLog")
 */
class ImportLogController extends AbstractFOSRestController
{
    /**
     * @var ImportLogRepository
     */
    private $importLogRepository;

    /**
     * ImportLogController constructor.
     * @param ImportLogRepository $importLogRepository
     */
    public function __construct(
        ImportLogRepository $importLogRepository
    ) {
        $this->importLogRepository = $importLogRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getYearAction()
    {
        $years = $this->importLogRepository->getYears();

        return new JsonResponse(['data' => $years]);
    }
}
