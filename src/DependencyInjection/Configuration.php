<?php

namespace Srebb\Bundle\SupervisorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** @var bool */
    private $debug;

    /**
     * @param bool $debug Whether to use the debug mode
     */
    public function __construct($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() : TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('srebb_supervisor');
            $rootNode    = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode    = $treeBuilder->root('srebb_supervisor');
        }

        $rootNode
            ->children()
                ->integerNode('connection_timeout')
                    ->defaultValue(5)->min(0)->max(60)
                ->end()

                ->arrayNode('update_interval')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('consumer')->defaultValue(2)->end()
                        ->scalarNode('logs')->defaultValue(10)->end()
                    ->end()
                ->end()

                ->arrayNode('server_list')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('host')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
