<?php


namespace App\Services;

use App\Entity\Columns\DataTextColumn;
use App\Entity\DataLine;
use App\Entity\ImportLog;
use App\Exceptions\ImportData\ImportModelDuplicatedHeaders;
use App\Exceptions\ImportData\SirenCarrierNotFoundException;
use App\Exceptions\ImportData\SirenRootNotFoundException;
use AthomeSolution\ImportBundle\Services\ConfigManager\ConfigManagerInterface;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;
use AthomeSolution\ImportBundle\Services\Detector\ImportDetector;
use AthomeSolution\ImportBundle\Services\FormatManager\FormatManagerInterface;
use AthomeSolution\ImportBundle\Services\ImportService;
use AthomeSolution\ImportBundle\Services\ValidatorManager;
use Doctrine\ORM\EntityManagerInterface;
use Port\Exception\DuplicateHeadersException;
use Port\Reader\CountableReader;

/**
 * Class ImportDataService
 * @package App\Services
 */
class ImportDataService extends ImportService
{
    /**
     * @param AbstractConfigSpecification $specification
     */
    public function addChainBlock(AbstractConfigSpecification $specification): void
    {
        if (!empty($this->responsibilityChain)) {
            $length = count($this->responsibilityChain) - 1;
            $this->responsibilityChain[$length]->setSuccessor($specification);
        }

        $this->responsibilityChain [] = $specification;
    }


    /**
     * @param \SplFileObject $file
     * @param ImportLog $importLog
     * @return array
     * @throws SirenCarrierNotFoundException
     * @throws SirenRootNotFoundException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function handleImport(\SplFileObject $file, ImportLog $importLog): array
    {
        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->detector->getConfigManager(
            $file,
            $importLog->getImportModel()->getConfig()->name,
            $this->source
        );

        /** @var FormatManagerInterface $formatManager */
        $formatManager = $this->detector->getFormatManager($file);
        try {
            $reader = $formatManager->getReader($file);
        } catch (DuplicateHeadersException $exception) {
            throw new ImportModelDuplicatedHeaders('duplicate_header');
        }

        $this->validateFile($reader, $configManager);

        return $this->readImportFile($reader, $configManager, $importLog);
    }

    /**
     * @param CountableReader $reader
     * @param ConfigManagerInterface $configManager
     * @param ImportLog $importLog
     * @return array
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception | SirenCarrierNotFoundException | SirenRootNotFoundException
     */
    public function readImportFile(
        CountableReader $reader,
        ConfigManagerInterface $configManager,
        ImportLog $importLog
    ): array
    {
        $this->entityManager->getConnection()->beginTransaction();
        $errors = [];
        try {
            foreach ($reader as $rowIndex => $row) {
                $dataLine = new DataLine();
                $dataLine->setImportLog($importLog);
                $objects = [
                    'dataLine' => $dataLine,
                    'department' => $importLog->getDepartment()->getId(),
                    'errors' => [],
                ];
                foreach ($row as $header => $value) {
                    $this->processResponsibilityChain($configManager, $header, $value, $rowIndex, $objects);
                }
                $errors = array_merge($errors, $objects['errors']);
            }

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception | SirenCarrierNotFoundException | SirenRootNotFoundException $exception) {
            $this->entityManager->getConnection()->rollBack();
            $importLog->setStatus('error');
            $this->entityManager->persist($importLog);
            $this->entityManager->flush();
            throw $exception;
        }

        return $errors;
    }

    /**
     * @param ConfigManagerInterface $configManager
     * @param string $header
     * @param string $value
     * @param int $rowIndex
     * @param array $objects
     * @return bool
     * @throws SirenRootNotFoundException | SirenCarrierNotFoundException | \Exception
     */
    public function processResponsibilityChain(
        ConfigManagerInterface $configManager,
        string $header,
        string $value,
        int $rowIndex,
        array &$objects
    ): bool
    {
        $configColumns = $configManager->getColumns();

        $column = $configColumns[$header] ?? new DataTextColumn();
        $data = [
            'column' => $column,
            'value' => $value,
            'rowIndex' => $rowIndex,
            'isMapped' => isset($configColumns[$header]),
        ];

        /** @var AbstractConfigSpecification $firstMember */
        $firstMember = $this->responsibilityChain[0];
        $firstMember->support($data, $objects);

        return true;
    }

    /**
     * @param CountableReader $reader
     * @param ConfigManagerInterface $configManager
     * @throws \Exception
     */
    public function validateFile(CountableReader $reader, ConfigManagerInterface $configManager): void
    {
        $configKeys = array_keys($configManager->getColumns());
        $currentRow = $reader->current();
        foreach ($configKeys as $configKey) {
            if (!isset($currentRow[$configKey])) {
                throw new \Exception('Le format du fichier n\'est pas conforme au modèle.');
            }
        }
    }
}
