<?php


namespace App\Services\Insee;

use App\Entity\ImportExport;
use App\Manager\ImportExportManager;
use Exception;
use Monolog\Logger;
use Port\Csv\CsvReader;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractInseeImport
 * @package App\Services\Insee
 */
abstract class AbstractInseeImport
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ImportExportManager
     */
    protected $importExportManager;

    /**
     * GroupImportService constructor.
     * @param LoggerInterface $logger
     * @param ImportExportManager $importExportManager
     */
    public function __construct(
        LoggerInterface $logger,
        ImportExportManager $importExportManager
    ) {

        $this->logger = $logger;
        $this->importExportManager = $importExportManager;
    }

    /**
     * @param ImportExport $task
     * @param array $parameters
     * @return ImportExport
     */
    public function import(ImportExport $task, array $parameters = [])
    {
        try {
            $readerCsv = $this->getReader($task->getFilePath() . $task->getFileName());
            $parameters['keys'] = $readerCsv->getColumnHeaders();

            $currentPage = $task->getCurrentPage();
            $limit = $currentPage + $task->getIncrement();

            $task->setCurrentPage($limit);
            $this->importExportManager->saveEntity($task);

            $readerCsv->seek($currentPage);

            for ($currentPage; $currentPage < $limit; $currentPage++) {
                if (!$readerCsv->valid()) {
                    $task->setState(ImportExport::STATE_SUCCESS);
                    $this->importExportManager->saveEntity($task);
                    break;
                }

                $this->processData($readerCsv->current(), $parameters);

                $readerCsv->next();
            }
        } catch (Exception $exception) {
            $task->setState(ImportExport::STATE_ERROR);
            $task->setErrorStack('On file : ' . $task->getFileName() . ' Exception : ' . $exception->getMessage());
            $this->importExportManager->saveEntity($task);
            $this->logger->log(Logger::CRITICAL, $exception->getMessage());
        }

        return $task;
    }


    /**
     * @param string $filePath
     * @return CsvReader
     */
    protected function getReader(string $filePath): CsvReader
    {
    }

    /**
     * @param array $data
     * @param array $parameters
     */
    protected function processData(array $data, array $parameters)
    {
    }
}
