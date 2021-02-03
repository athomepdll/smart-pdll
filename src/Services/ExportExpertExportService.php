<?php


namespace App\Services;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ExportExpertExportService
 * @package App\Services
 */
class ExportExpertExportService
{
    /**
     * @var DataExportService
     */
    private $dataExportService;


    /**
     * ExportExpertExportService constructor.
     * @param DataExportService $dataExportService
     */
    public function __construct(
        DataExportService $dataExportService
    ) {
        $this->dataExportService = $dataExportService;
    }

    /**
     * @param array $filters
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function export(array $filters)
    {
        $filters['dataLevel'] = ["summary", "detail"];
        return $this->dataExportService->exportImportModelCsv($filters['importModel'], $filters);
    }
}
