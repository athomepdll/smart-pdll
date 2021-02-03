<?php


namespace App\Handler\ExportInsee;

use App\Handler\AbstractHandler;
use App\Services\Insee\RegionalPerimeterExportService;
use Symfony\Component\Form\FormInterface;

/**
 * Class ExportInseeHandler
 * @package App\Handler\ExportInsee
 */
class ExportInseeHandler extends AbstractHandler
{
    /**
     * @var RegionalPerimeterExportService
     */
    private $regionalPerimeterExport;

    /**
     * ExportInseeHandler constructor.
     * @param RegionalPerimeterExportService $regionalPerimeterExport
     */
    public function __construct(
        RegionalPerimeterExportService $regionalPerimeterExport
    ) {
        $this->regionalPerimeterExport = $regionalPerimeterExport;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        return parent::validate($form); // TODO: Change the autogenerated stub
    }

    /**
     * @param FormInterface $form
     * @return mixed
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return null;
        }

        $filters = $form->getData();

        return $this->regionalPerimeterExport->export($filters);
    }
}