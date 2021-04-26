<?php


namespace AthomeSolution\ImportBundle\DependencyInjection\Compiler;


use AthomeSolution\ImportBundle\DependencyInjection\Configuration;
use AthomeSolution\ImportBundle\Factory\FactoryConfigManager;
use AthomeSolution\ImportBundle\Services\ConfigManager\ConfigManager;
use AthomeSolution\ImportBundle\Services\Detector\ImportDetector;
use AthomeSolution\ImportBundle\Services\ImportService;
use AthomeSolution\ImportBundle\Services\ValidatorManager;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ImportBundleCompilerPass
 * @package AthomeSolution\ImportBundle\DependencyInjection\Compiler
 */
class ImportBundleCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('athome_solution_import_bundle');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->setBundleMap($config, $container);
        $this->generateImportService($config, $container);
        $this->generateFormatManagers($config, $container);
        $this->generateConfigManagers($config, $container);
    }

    private function setBundleMap(array $config, ContainerBuilder $container)
    {
        $container->setParameter('bundle.map', $config['map']);
    }

    /**
     * @return Definition
     */
    private function generateAndGetFactory()
    {
        $factoryDefinition = new Definition(FactoryConfigManager::class);
        $factoryDefinition->setPublic(true);
        $factoryDefinition->addTag('athome_import.factory_manager');

        return $factoryDefinition;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function generateConfigManagers(array $config, ContainerBuilder &$container)
    {
        $factoryDefinition = $this->generateAndGetFactory();
        foreach ($config['configs'] as $key => $datum) {

            $definition = new Definition(ConfigManager::class);
            $definition->setPublic(true);
            $definition->addMethodCall('generateColumns', [$datum['columns']]);
            $definition->addTag('athome_import.config');
            $factoryDefinition->addMethodCall('addConfigInformation', ['athome_import.' . $key, $datum['pattern']]);
            $container->setDefinition('athome_import.' . $key, $definition);
            $container->setDefinition('athome_import.factory_config_manager', $factoryDefinition);
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function generateFormatManagers(array $config, ContainerBuilder &$container)
    {
        foreach ($config['format'] as $format) {
            if ($container->has('athome_import.format_manager.' . $format)) {

                $definition = $container->getDefinition('athome_import.format_manager.' . $format);
                $definition->setPublic(true);

                $this->generateFormatResponsibilityChain($config, $container, $definition, $format);
                $container->setDefinition('athome_import.format_manager.' . $format, $definition);
            }
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function generateImportService(array $config, ContainerBuilder &$container)
    {
        $definition = new Definition($config['import_service_class'], [
            new Reference(ImportDetector::class),
            new Reference(ValidatorManager::class),
            new Reference('doctrine.orm.default_entity_manager')
        ]);

        $definition->setPublic(true);
        $definition->addMethodCall('setSource', [$config['source']]);
        $this->generateImportServiceResponsibilityChain($config, $container, $definition);
        $container->setDefinition('athome_solution.import_service', $definition);
    }

    /**
     * @param array $config
     * @param $container
     * @param Definition $definition
     */
    private function generateImportServiceResponsibilityChain(array $config, ContainerBuilder $container, Definition &$definition)
    {
        $taggedServices = $container->findTaggedServiceIds('athome_import.config_specification');

        if (!$config['load_default']) {
            $defaultTaggedServices = $container->findTaggedServiceIds('athome_import.default');
            $taggedServices = array_diff_key($taggedServices, $defaultTaggedServices);
        }
        $taggedServices = $this->sortTaggedServices($taggedServices);

        foreach ($taggedServices as $idTaggedService) {
            $definition->addMethodCall('addChainBlock', [new Reference($idTaggedService)]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $definition
     * @param string $format
     */
    private function generateFormatResponsibilityChain(
        array $config,
        ContainerBuilder $container,
        Definition &$definition,
        string $format
    ) {
        $taggedServices = $container->findTaggedServiceIds('athome_import.format_specification.' . $format);

        if (!$config['load_default']) {
            $defaultTaggedServices = $container->findTaggedServiceIds('athome_import.format_specification_default');
            $taggedServices = array_diff_key($taggedServices, $defaultTaggedServices);
        }

        foreach ($taggedServices as $id => $taggedService) {
            $definition->addMethodCall('addChainBlock', [new Reference($id)]);
        }
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param array $configs
     * @return array
     */
    private function processConfiguration(ConfigurationInterface $configuration, array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }

    private function sortTaggedServices(array $taggedServicesArray)
    {
        foreach ($taggedServicesArray as $key => $taggedService)  {
            $sortedTaggedServices[$taggedService[0]['priority']] = $key;
        }

        ksort($sortedTaggedServices);

        return $sortedTaggedServices;
    }
}