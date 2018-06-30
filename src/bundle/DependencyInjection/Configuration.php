<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;

class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('edgar_ez_web_push');

        $rootNode
            ->children()
                ->scalarNode('subject')
                    ->isRequired()
                ->end()
                ->scalarNode('vapid_public_key')
                    ->isRequired()
                ->end()
                ->scalarNode('vapid_private_key')
                    ->isRequired()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
