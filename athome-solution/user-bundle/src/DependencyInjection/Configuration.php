<?php


namespace Athome\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('athome_user');

        $rootNode
            ->children()
            ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('from_email')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('enable_on_registration')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
