<?php


namespace AthomeSolution\ImportBundle\Services;


use AthomeSolution\ImportBundle\Services\ConfigManager\ConfigManagerInterface;
use AthomeSolution\ImportBundle\Services\Detector\ImportDetector;
use AthomeSolution\ImportBundle\Services\FormatManager\FormatManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Port\Reader\CountableReader;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class ImportService
 * @package AthomeSolution\ImportBundle\Services
 */
class ImportService
{
    const SOURCE_YAML = 'yaml';
    const SOURCE_DATABASE = 'database';

    /**
     * @var ImportDetector
     */
    protected $detector;
    /**
     * @var ValidatorManager
     */
    protected $validatorManager;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /** @var array */
    protected $responsibilityChain;

    /** @var string */
    protected $source;

    /**
     * ImportService constructor.
     * @param ImportDetector $detector
     * @param ValidatorManager $validatorManager
     */
    public function __construct(
        ImportDetector $detector,
        ValidatorManager $validatorManager,
        EntityManagerInterface $entityManager
    )
    {
        $this->detector = $detector;
        $this->validatorManager = $validatorManager;
        $this->entityManager = $entityManager;
        $this->responsibilityChain = [];
    }

    /**
     * @param string $source
     */
    public function setSource(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param AbstractConfigSpecification $specification
     */
    public function addChainBlock(AbstractConfigSpecification $specification)
    {
        if (!empty($this->responsibilityChain))
        {
            $length = count($this->responsibilityChain)-1;
            $this->responsibilityChain[$length]->setSuccessor($specification);
        }

        $this->responsibilityChain []= $specification;
    }

    /**
     * @param UploadedFile $file
     * @param string $configName
     * @throws \Exception
     */
    public function handle(\SplFileObject $file, string $configName)
    {
        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->detector->getConfigManager($file, $configName, $this->source);
        /** @var FormatManagerInterface $formatManager */
        $formatManager = $this->detector->getFormatManager($file);
        $reader = $formatManager->getReader($file);

        $this->readFile($reader, $configManager, $formatManager);
    }

    public function readFile(
        CountableReader $reader,
        ConfigManagerInterface $configManager
    ) {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($reader as $rowIndex => $row) {
                $objects = [];
                foreach ($row as $header => $value) {
                    $this->processResponsibilityChain($configManager, $header, $value, $rowIndex, $objects);
                }
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $exception) {
            $this->entityManager->getConnection()->rollBack();
            throw $exception;
        }
    }

    /**
     * @param ConfigManagerInterface $configManager
     * @param string $header
     * @param string $value
     * @param array $objects
     * @return boolean
     */
    public function processResponsibilityChain(ConfigManagerInterface $configManager, string $header, string $value, int $rowIndex, array &$objects)
    {
        $configColumns = $configManager->getColumns();

        if (!isset($configColumns[$header])) {
            return false;
        }

        $column = $configColumns[$header];
        $data = [
            'column' => $column,
            'value' => $value
        ];

        if (!$column) {
            return false;
        }

        /** @var AbstractConfigSpecification $firstMember*/
        $firstMember = $this->responsibilityChain[0];
        $firstMember->support($data, $objects);

        return true;
    }
}