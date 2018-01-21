<?php

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
                ->arrayNode('manifest')
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                        ->end()
                        ->scalarNode('short_name')
                            ->isRequired()
                        ->end()
                        ->scalarNode('api_key')
                            ->isRequired()
                        ->end()
                        ->scalarNode('gcm_sender_id')
                            ->isRequired()
                        ->end()
                    ->end()
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
