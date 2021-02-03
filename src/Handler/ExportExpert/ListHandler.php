<?php


namespace App\Handler\ExportExpert;

use App\Handler\AbstractHandler;
use App\Repository\ImportLogRepository;
use Symfony\Component\Form\FormInterface;

/**
 * Class ListHandler
 * @package App\Handler\ExportExpert
 */
class ListHandler extends AbstractHandler
{

    /**
     * @var ImportLogRepository
     */
    private $importLogRepository;

    /**
     * ListHandler constructor.
     * @param ImportLogRepository $importLogRepository
     */
    public function __construct(
        ImportLogRepository $importLogRepository
    ) {
        $this->importLogRepository = $importLogRepository;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        if (!$form->get('submit')->isClicked()) {
            return false;
        }

        return true;
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

        $data = $form->getData();
        $year = $data->getYear();
        $importModel = $data->getImportModel();
        $importLogsQuery = $this->importLogRepository->getByImportModelAndYear($year, $importModel);

        return $importLogsQuery->getQuery()->getResult();
    }
}
