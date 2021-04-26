<?php


namespace AthomeSolution\ImportBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class AthomeSolutionImportBundleExtension
 * @package AthomeSolution\ImportBundle\DependencyInjection
 */
class AthomeSolutionImportBundleExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {

//        $loader = new YamlFileLoader(
//            $container,
//            new FileLocator(__DIR__.'/../Resources/config')
//        );
//        $loader->load('athome_solution_import_bundle.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
    }
}