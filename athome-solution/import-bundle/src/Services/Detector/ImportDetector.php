<?php


namespace AthomeSolution\ImportBundle\Services\Detector;


use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Repository\ColumnRepository;
use AthomeSolution\ImportBundle\Repository\ConfigRepository;
use AthomeSolution\ImportBundle\Services\ConfigManager\ConfigManager;
use AthomeSolution\ImportBundle\Services\ConfigManager\ConfigManagerInterface;
use AthomeSolution\ImportBundle\Services\FormatManager\FormatManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImportDetector
 * @package AthomeSolution\ImportBundle\Services\Detector
 */
class ImportDetector implements DetectorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var ColumnRepository
     */
    private $columnRepository;

    /**
     * ImportDetector constructor.
     * @param ContainerInterface $container
     * @param ConfigRepository $configRepository
     * @param ColumnRepository $columnRepository
     */
    public function __construct(
        ContainerInterface $container,
        ConfigRepository $configRepository,
        ColumnRepository $columnRepository
    ) {
        $this->container = $container;
        $this->configRepository = $configRepository;
        $this->columnRepository = $columnRepository;
    }

    /**
     * @param \SplFileObject  $file
     * @return object
     */
    public function getFormatManager(\SplFileObject  $file)
    {
        $type = $file->getExtension();
        if (!$this->container->has('athome_import.format_manager.' . $type)) {
            throw new \Exception('Le fichier ne possède pas le bon format.');
        }
        return $this->container->get('athome_import.format_manager.' . $type);
    }

    /**
     * @param UploadedFile $file
     * @param string $configName
     * @param string $source
     * @return object
     */
    public function getConfigManager(\SplFileObject $file, $configName = 'default', $source = 'yaml')
    {
        if ($source == 'yaml') {
            return $this->getByYamlConfiguration($file, $configName);
        }

        return $this->getByDatabaseConfiguration($file, $configName);
    }

    /**
     * @param \SplFileObject  $file
     * @param string $configName
     * @return null
     */
    protected function getByYamlConfiguration(\SplFileObject $file, string $configName)
    {
        if ($configName !== 'default') {
            $guessedConfigNameService = 'athome_import.' . $configName;
            if (!$this->container->has($guessedConfigNameService)) {
                return null;
            }
            return $this->container->get('athome_import.' . $configName);
        }

        $factoryConfig = $this->container->get('athome_import.factory_config_manager');
        $filename = $file->getFilename();

        foreach ($factoryConfig->configInformation as $key => $pattern) {
            if ((preg_match($pattern, $filename) || $pattern === null) && $this->container->has($key)) {
                return $this->container->get($key);
            }
        }
    }

    /**
     * @param \SplFileObject  $file
     * @param string $configName
     * @return ConfigManager
     */
    protected function getByDatabaseConfiguration(\SplFileObject  $file, string $configName)
    {
        $configManager = new ConfigManager();
        /** @var Config $config */
        $config = $this->configRepository->getByConfigName($configName);
        if ($config) {
            $columns = $this->columnRepository->getByConfig($config->id);
            $configManager->setColumns($columns);
        }
        return $configManager;
    }
}