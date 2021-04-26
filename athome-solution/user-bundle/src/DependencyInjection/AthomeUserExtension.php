<?php

namespace Athome\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AthomeUserExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

//        $this->loadDoctrineConfig($configs, $container);
        $this->loadServiceConfig($configs, $container);

        $config = $processor->processConfiguration($configuration, $configs);

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                'user_class' => 'user_bundle.user_class',
                'from_email' => 'user_bundle.from_email',
                'enable_on_registration' => 'user_bundle.enable_on_registration',
            ),
        ));
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $namespaces
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    private function loadDoctrineConfig(array $configs, ContainerBuilder $container)
    {
        // Load doctrine mapping
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/doctrine')
        );

        $loader->load('User.orm.xml', 'orm');
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    private function loadServiceConfig(array $configs, ContainerBuilder $container)
    {
        // Load doctrine mapping
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/services/')
        );

        $loader->load('services.yaml');
    }
}
