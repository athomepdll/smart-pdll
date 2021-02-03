<?php


namespace App\Handler\Data;

use App\Entity\ImportModel;
use App\Handler\AbstractHandler;
use App\Repository\DataColumnRepository;
use App\Repository\DataRepository;
use App\Repository\ImportModelRepository;
use App\Services\DataViewService;
use Symfony\Component\Form\FormInterface;

/**
 * Class DataHandler
 * @package App\Handler\Data
 */
class DataHandler extends AbstractHandler
{
    /**
     * @var DataColumnRepository
     */
    private $dataColumnRepository;
    /**
     * @var DataRepository
     */
    private $dataRepository;
    /**
     * @var ImportModelRepository
     */
    private $importModelRepository;
    /**
     * @var DataViewService
     */
    private $dataViewService;

    /**
     * DataHandler constructor.
     * @param DataColumnRepository $dataColumnRepository
     * @param DataRepository $dataRepository
     * @param DataViewService $dataViewService
     * @param ImportModelRepository $importModelRepository
     */
    public function __construct(
        DataColumnRepository $dataColumnRepository,
        DataRepository $dataRepository,
        DataViewService $dataViewService,
        ImportModelRepository $importModelRepository
    ) {
        $this->dataColumnRepository = $dataColumnRepository;
        $this->dataRepository = $dataRepository;
        $this->dataViewService = $dataViewService;
        $this->importModelRepository = $importModelRepository;
    }

    /**
     * @param FormInterface $form
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return [];
        }

        $filters = $form->getData();

        $importModels['indicator'] = $filters['indicatorImportModels'];
        $importModels['financial'] = $filters['financialImportModels'];
        $data = ['indicator' => [], 'financial' => []];

        /** @var ImportModel $importModel */
        foreach ($importModels as $key => $importModelsType) {
            $filters['isFinancial'] = $key === 'financial';
            foreach ($importModelsType as $importModel) {
                $crosstabedData = $this->dataViewService->createCrosstabView($importModel, $filters);
                $data[$key] = array_merge($data[$key], $crosstabedData);
            }
        }

        return $data;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        return parent::validate($form); // TODO: Change the autogenerated stub
    }
}