<?php

namespace Zurd2\SmallCrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('small_crud');

        $this->addConfigMenu($rootNode);

        return $treeBuilder;
    }

    public function addConfigMenu(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('login')
                    ->children()
                        ->scalarNode('path')
                            ->defaultValue('app_login_page')
                            ->info('Path of login page')
                            ->example('app_login_page')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('menu')
                    ->normalizeKeys(false)
                    ->defaultValue(array())
                    ->info('The items to display in the main menu.')
                    ->prototype('variable')
                ->end()
            ->end()
        ;
    }
}
