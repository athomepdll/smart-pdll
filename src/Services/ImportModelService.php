<?php


namespace App\Services;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\SirenCarrierColumn;
use App\Entity\Columns\SirenRootColumn;
use App\Entity\ImportModel;
use App\Factory\ImportModelFactory;
use App\Manager\DataColumnManager;
use App\Manager\ImportModelManager;
use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Manager\ConfigManager;

/**
 * Class ImportModelService
 * @package App\Services
 */
class ImportModelService
{
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var ImportModelFactory
     */
    private $importModelFactory;
    /**
     * @var ImportModelManager
     */
    private $importModelManager;
    /**
     * @var DataColumnManager
     */
    private $dataColumnManager;

    /**
     * ImportModelService constructor.
     * @param ConfigManager $configManager
     * @param DataColumnManager $dataColumnManager
     * @param ImportModelFactory $importModelFactory
     * @param ImportModelManager $importModelManager
     */
    public function __construct(
        ConfigManager $configManager,
        DataColumnManager $dataColumnManager,
        ImportModelFactory $importModelFactory,
        ImportModelManager $importModelManager
    ) {
        $this->configManager = $configManager;
        $this->dataColumnManager = $dataColumnManager;
        $this->importModelFactory = $importModelFactory;
        $this->importModelManager = $importModelManager;
    }

    /**
     * @return ImportModel
     */
    public function generateImportModel()
    {
        $config = new Config();
        $config->name = "import_model";

        $this->configManager->saveEntity($config);

        $importModel = $this->importModelFactory->create();
        $importModel->setConfig($config);

        $sirenCarrier = new SirenCarrierColumn();
        $sirenCarrier->setIdentifier(SirenCarrierColumn::SIREN_CARRIER_IDENTIFIER);
        $sirenCarrier->setConfig($config);

        $this->dataColumnManager->saveEntity($sirenCarrier);

        $sirenRoot = new SirenRootColumn();
        $sirenRoot->setIdentifier(SirenRootColumn::SIREN_ROOT_IDENTIFIER);
        $sirenRoot->setConfig($config);

        $this->dataColumnManager->saveEntity($sirenRoot);

        return $importModel;
    }
}
