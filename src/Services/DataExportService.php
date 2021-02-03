<?php


namespace App\Services;

use App\Entity\DataLevel;
use App\Entity\FinancialField;
use App\Entity\ImportModel;
use App\Factory\Query\ColumnsHeaderFactory;
use App\Factory\Query\DataExportQueryFactory;
use App\Factory\Query\MetaDataQueryFactory;
use App\Repository\DataRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;

/**
 * Class DataExportService
 * @package App\Services
 */
class DataExportService
{
    private const METADATA = [
        'Date de la donnée' => '"Date de la donnée" varchar',
        'Fournisseur de la donnée' => '"Fournisseur de la donnée" varchar',
        'Service emetteur' => '"Service emetteur" varchar',
        'Nom émetteur' => '"Nom émetteur" varchar',
        'Prénom émetteur' => '"Prénom émetteur" varchar',
        'Mail émetteur' => '"Mail émetteur"varchar',
        'Téléphone émetteur' => '"Téléphone émetteur" varchar',
    ];

    private const HEADERS = [
        'id' => 'id int',
        'Nom Commune' => '"Nom Commune" varchar',
        'Nom structure porteuse' => '"Nom structure porteuse" varchar',
        'Modèle d\'import' => '"Modèle d\'import" varchar',
        'Année' => '"Année" int',
    ];

    /**
     * @var DataExportQueryFactory
     */
    private $dataExportFacory;

    /**
     * @var DataRepository
     */
    private $dataRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ColumnsHeaderFactory
     */
    private $columnsHeadersQueryFactory;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var string
     */
    private $exportDir;
    /**
     * @var MetaDataQueryFactory
     */
    private $metaDataQueryFactory;

    /**
     * DataExportService constructor.
     * @param DataRepository $dataRepository
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     * @param string $exportDir
     */
    public function __construct(
        DataRepository $dataRepository,
        EntityManagerInterface $entityManager,
        string $exportDir,
        KernelInterface $kernel
    ) {
        $this->dataExportFacory = new DataExportQueryFactory();
        $this->columnsHeadersQueryFactory = new ColumnsHeaderFactory();
        $this->metaDataQueryFactory = new MetaDataQueryFactory();
        $this->dataRepository = $dataRepository;
        $this->entityManager = $entityManager;
        $this->exportDir = $exportDir;
        $this->kernel = $kernel;
    }

    /**
     * @param ImportModel $importModel
     * @param array $filters
     * @return array
     * @throws DBALException
     */
    public function getColumnsName(ImportModel $importModel, array $filters)
    {
        if (isset($filters['dataLevel']) && !is_array($filters['dataLevel'])) {
            return $this->dataRepository->getHeadersColumn($importModel, DataLevel::SUMMARY);
        }

        return $this->dataRepository->getHeadersColumnByDataLevels($importModel, $filters['dataLevel']);
    }

    /**
     * @param array $columnsName
     * @return string
     */
    private function getCrosstabHeaders(array $columnsName)
    {
        $keys = array_keys(self::HEADERS);
        $headers = join(', ', self::HEADERS);

        foreach ($columnsName as $columnName) {
            if (in_array($columnName, $keys)) {
                continue;
            }

            $type = isset(FinancialField::TYPE[$columnName]) ? FinancialField::TYPE[$columnName] : 'varchar';
            $headers .= ', "' . $columnName . '" ' . $type;
        }
        return $headers;
    }

    /**
     * @param array $filters
     * @return false|string
     * @throws DBALException
     * @throws Exception
     */
    public function exportOdsVisualization(array $filters)
    {
        $spreadsheet = $this->createOdsExport();
        $importModels['indicator'] = $filters['indicatorImportModels'];
        $importModels['financial'] = $filters['financialImportModels'];

        /** @var ImportModel $importModel */
        foreach ($importModels as $key => $importModelsType) {
            $filters['isFinancial'] = $key === 'financial';
            foreach ($importModelsType as $importModel) {
                $this->exportImportModelOds($filters, $importModel, $spreadsheet);
            }
        }

        return $this->saveAndGetFilename($filters, $spreadsheet);
    }

    /**
     * @return Spreadsheet
     */
    private function createOdsExport()
    {
        return new Spreadsheet();
    }

    /**
     * @param array $filters
     * @param Spreadsheet $spreadsheet
     * @return false|string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws Exception
     */
    private function saveAndGetFilename(array $filters, Spreadsheet &$spreadsheet)
    {
        $spreadsheet->removeSheetByIndex(0);
        $yearStart = $filters['yearStart'];
        $yearEnd = isset($filters['yearEnd']) ? '-' . $filters['yearEnd'] : '';
        $date = new \DateTime('now');
        $filename = "{$date->format('d-m-Y')}-Export-{$yearStart}-{$yearEnd}.zip";
        $filePath = $this->kernel->getProjectDir() . $this->exportDir . $filename;

        $writer = new Ods($spreadsheet);
        $writer->save($filePath);

        return $filename;
    }

    /**
     * @param array $filters
     * @param ImportModel $importModel
     * @param Spreadsheet $spreadsheet
     * @throws DBALException
     * @throws Exception
     */
    public function exportImportModelOds(array $filters, ImportModel $importModel, Spreadsheet &$spreadsheet)
    {
        $filters['importModel'] = $importModel;

        $crosstabedData = $this->getCrosstabedData($importModel, $filters);
        $metaData = $this->getMetaData($filters);

        $this->generateOdsPage($crosstabedData, $metaData, $importModel, $spreadsheet);
    }

    /**
     * @param ImportModel $importModel
     * @param array $filters
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exportImportModelCsv(ImportModel $importModel, array $filters)
    {
        $filters['importModel'] = $importModel;

        $crosstabedData = $this->getCrosstabedData($importModel, $filters);

        return $this->generateCsv($importModel, $crosstabedData);
    }

    /**
     * @param ImportModel $importModel
     * @param array $filters
     * @return mixed[]
     * @throws DBALException
     */
    private function getCrosstabedData(ImportModel $importModel, array $filters)
    {

        $columnsName = $this->getColumnsName($importModel, $filters);
        $headers = $this->getCrosstabHeaders($columnsName);

        $sourceQuery = $this->dataExportFacory->create($filters);
        $categoryQuery = $this->columnsHeadersQueryFactory->create($filters);

        $sql = "select * from crosstab($$ {$sourceQuery} $$, $$ {$categoryQuery} $$) as ({$headers})";

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param ImportModel $importModel
     * @param array $filters
     * @return mixed[]
     * @throws DBALException
     */
    private function getMetaData(array $filters)
    {
        $metaQuery = $this->metaDataQueryFactory->create($filters);
        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($metaQuery);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $data
     * @param array $metaData
     * @param ImportModel $importModel
     * @param Spreadsheet $spreadsheet
     * @throws Exception
     */
    private function generateOdsPage(array $data, array $metaData, ImportModel $importModel, Spreadsheet &$spreadsheet)
    {
        $worksheet = new Worksheet($spreadsheet, $importModel->getName());
        $filters['importModel'] = $importModel;

        $this->writeData($worksheet, $metaData, 'A', 1);
        $this->writeData($worksheet, $data, 'A', count($metaData) + 3);

        $spreadsheet->addSheet($worksheet);
    }

    /**
     * @param Worksheet $worksheet
     * @param array $data
     * @param string $startColumn
     * @param int $startRow
     * @throws Exception
     */
    private function writeData(Worksheet &$worksheet, array $data, string $startColumn, int $startRow)
    {
        $keys = array_keys($data[0]);

        $worksheet->fromArray($keys, null, $startColumn . $startRow);

        foreach ($data as $datum) {
            $startRow++;
            $worksheet->fromArray($datum, null, $startColumn . $startRow);
        }
    }

    /**
     * @param ImportModel $importModel
     * @param array $data
     * @return string
     */
    private function generateCsv(ImportModel $importModel, array $data)
    {
        $exportPath = $this->kernel->getProjectDir() . $this->exportDir . $importModel->getName() . '.csv';

        $file = fopen($exportPath, 'w');
        fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        if (!empty($data)) {
            $keys = array_keys($data[0]);
            fputcsv($file, $keys);

            foreach ($data as $datum) {
                fputcsv($file, $datum);
            }
        }

        fclose($file);

        return $exportPath;
    }
}
