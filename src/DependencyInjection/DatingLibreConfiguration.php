<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DatingLibreConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dating_libre');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('categories')->scalarPrototype()->end()->end()
                ->arrayNode('attributes')->arrayPrototype()->scalarPrototype()->end()->end()->end()
                ->arrayNode('block_reasons')->scalarPrototype()->end()->end();

        return $treeBuilder;
    }
}
