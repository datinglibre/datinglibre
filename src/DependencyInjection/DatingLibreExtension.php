<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class DatingLibreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->processConfiguration(new DatingLibreConfiguration(), $configs);
        $container->setParameter('datinglibre.categories', $configuration['categories']);
        $container->setParameter('datinglibre.attributes', $configuration['attributes']);
        $container->setParameter('datinglibre.block_reasons', $configuration['block_reasons']);
    }
}
