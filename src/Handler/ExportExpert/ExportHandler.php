<?php


namespace App\Handler\ExportExpert;

use App\Handler\AbstractHandler;
use App\Services\ExportExpertExportService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ExportHandler
 * @package App\Handler\ExportExpert
 */
class ExportHandler extends AbstractHandler
{
    /**
     * @var ExportExpertExportService
     */
    private $exportExpertExportService;

    /**
     * ExportHandler constructor.
     * @param ExportExpertExportService $exportExpertExportService
     */
    public function __construct(
        ExportExpertExportService $exportExpertExportService
    ) {
        $this->exportExpertExportService = $exportExpertExportService;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        if (!$form->get('export')->isClicked()) {
            return false;
        }

         return true;
    }

    /**
     * @param FormInterface $form
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return null;
        }

        $dateTime = new \DateTime('now');
        $data = $form->getData();

        $filters['yearStart'] = $data->getYear();
        $filters['importModel'] = $data->getImportModel();
        $realFilePath = $this->exportExpertExportService->export($filters);

        $response = new BinaryFileResponse($realFilePath);
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $dateTime->format('Y-m-d') . '-' . $filters['importModel']->getName() . '-' . $filters['yearStart'] . '.csv'
        );

        return $response;
    }
}
