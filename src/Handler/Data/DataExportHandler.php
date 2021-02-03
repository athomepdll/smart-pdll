<?php


namespace App\Handler\Data;

use App\Handler\AbstractHandler;
use App\Services\DataExportService;
use Symfony\Component\Form\FormInterface;

/**
 * Class DataExportHandler
 * @package App\Handler\Data
 */
class DataExportHandler extends AbstractHandler
{
    /**
     * @var DataExportService
     */
    private $dataExportService;

    /**
     * DataExportHandler constructor.
     * @param DataExportService $dataExportService
     */
    public function __construct(
        DataExportService $dataExportService
    ) {
        $this->dataExportService = $dataExportService;
    }

    /**
     * @param FormInterface $form
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        $filters = $form->getData();

        return $this->dataExportService->exportOdsVisualization($filters);
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        return parent::validate($form);
    }
}
