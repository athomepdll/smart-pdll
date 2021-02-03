<?php


namespace App\Handler\ImportInsee;

use App\Entity\ImportExport;
use App\Handler\AbstractHandler;
use App\Repository\ImportExportRepository;
use Symfony\Component\Form\FormInterface;

/**
 * Class ImportInseeStatusHandler
 * @package App\Handler
 */
class ImportInseeStatusHandler extends AbstractHandler
{

    /**
     * @var ImportExportRepository
     */
    private $importExportRepository;

    /**
     * ImportInseeStatusHandler constructor.
     * @param ImportExportRepository $importExportRepository
     */
    public function __construct(
        ImportExportRepository $importExportRepository
    ) {
        $this->importExportRepository = $importExportRepository;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function handle(FormInterface $form)
    {
        $status = ['loading' => false, 'errors' => null];
        if (!$this->validate($form)) {
            return $status;
        }

        $result = $this->importExportRepository->findRunnableTaskByType(ImportExport::INSEE_IMPORT_TYPE);
        $errors = $this->importExportRepository->findErrorTaskByType(ImportExport::INSEE_IMPORT_TYPE);

        if (!empty($errors)) {
            $status['errors'] = array_reduce($errors, function ($message, ImportExport $importExport) {
                return $importExport->getErrorStack() . ' ' . $message;
            }, '');
        }

        $status['loading'] = !empty($result);

        return $status;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {

        $data = $form->getData();

        if ($data['type'] == null) {
            return false;
        }

        return true;
    }
}
