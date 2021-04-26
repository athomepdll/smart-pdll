<?php


namespace AthomeSolution\ImportBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package AthomeSolution\ImportBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('athome_solution_import_bundle');
        $rootNode
            ->children()
                ->arrayNode('map')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('import_service_class')->defaultValue('AthomeSolution\ImportBundle\Services\ImportService')->end()
                ->scalarNode('source')->defaultNull()->end()
                ->scalarNode('load_default')->defaultTrue()->end()
                ->scalarNode('detector')->defaultNull()->end()
                ->scalarNode('factory')->defaultNull()->end()
                ->arrayNode('format')->scalarPrototype()->end()->end()
                ->arrayNode('configs')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('pattern')->end()
                            ->arrayNode('columns')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('type')->end()
                                        ->scalarNode('identifier')->end()
                                        ->scalarNode('columnName')->end()
                                        ->scalarNode('class')->end()
                                        ->scalarNode('method')->end()
                                        ->scalarNode('attribute')->end()
                                        ->scalarNode('identifier')->end()
                                        ->scalarNode('validator')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;


        return $treeBuilder;
    }
}