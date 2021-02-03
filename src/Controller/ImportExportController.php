<?php


namespace App\Controller;

use App\Form\ImportInseeStatusType;
use App\Handler\ImportInsee\ImportInseeStatusHandler;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class ImportExportController
 * @package App\Controller\Rest
 *
 * @Rest\RouteResource("ImportExport")
 */
class ImportExportController extends AbstractFOSRestController
{

    /**
     * @var ImportInseeStatusHandler
     */
    private $importInseeStatusHandler;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ImportExportController constructor.
     * @param ImportInseeStatusHandler $importInseeStatusHandler
     * @param RequestStack $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(
        ImportInseeStatusHandler $importInseeStatusHandler,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->importInseeStatusHandler = $importInseeStatusHandler;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postIsrunningAction(Request $request)
    {
        $status = ['result' => false, 'errors' => null];
        $httpStatus = 200;

        $form = $this->createForm(ImportInseeStatusType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $status = $this->importInseeStatusHandler->handle($form);
            } catch (\Exception $exception) {
                $this->logger->log('error', $exception->getMessage());
                $httpStatus = 500;
            }
        }

        return new JsonResponse(['loading' => $status['loading'], 'errors' => $status['errors']], $httpStatus);
    }
}
