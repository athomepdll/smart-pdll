<?php


namespace App\Controller;

use App\Entity\Enumeration;
use App\Manager\EnumerationManager;
use App\Repository\EnumerationRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DataLevelController
 * @package App\Controller
 *
 * @Rest\RouteResource("Enumeration")
 */
class EnumerationController extends AbstractFOSRestController
{
    /**
     * @var EnumerationRepository
     */
    private $enumerationRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EnumerationManager
     */
    private $enumerationManager;

    /**
     * EnumerationController constructor.
     * @param EnumerationManager $enumerationManager
     * @param EnumerationRepository $enumerationRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        EnumerationManager $enumerationManager,
        EnumerationRepository $enumerationRepository,
        LoggerInterface $logger
    ) {
        $this->enumerationManager = $enumerationManager;
        $this->enumerationRepository = $enumerationRepository;
        $this->logger = $logger;
    }

    /**
     * @Rest\QueryParam(name="discr", requirements="[a-z_]+")
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $enumerations = [];
        $httpStatus = 200;

        try {
            $enumerations = $this->enumerationRepository->getArrayEnumerations($paramFetcher->all());
        } catch (\Exception $exception) {
            $this->logger->critical('API : ' .$exception->getMessage());
        }

        return new JsonResponse(['enumerations' => $enumerations], $httpStatus);
    }

    /**
     * @Rest\RequestParam(name="discr")
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="value")
     * @param ParamFetcher $paramFetcher
     */
    public function cpatchAction(ParamFetcher $paramFetcher)
    {
        try {
            $paramFetcher->get('discr');
            $paramFetcher->get('name');
            $paramFetcher->get('value');
        } catch (\InvalidArgumentException $exception) {
            $this->logger->critical('API : ' .$exception->getMessage());
        }

        try {
            $enumeration = $this->enumerationRepository->getEnumeration($paramFetcher->all());
            if (!$enumeration instanceof Enumeration) {
                throw new \Exception('Not found');
            }
            $value = $paramFetcher->get('value') ?? '';
            $enumeration->setValue($value);
            $this->enumerationManager->saveEntity($enumeration);
        } catch (\Exception $exception) {
            $this->logger->critical('API : ' .$exception->getMessage());
        }
    }
}
