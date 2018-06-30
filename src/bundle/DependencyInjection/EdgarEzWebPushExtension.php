<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EdgarEzWebPushExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('edgar_ez_web_push.subject', $config['subject'] ?? null);
        $container->setParameter('edgar_ez_web_push.vapid_public_key', $config['vapid_public_key'] ?? null);
        $container->setParameter('edgar_ez_web_push.vapid_private_key', $config['vapid_private_key'] ?? null);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('assetic', ['bundles' => ['EdgarEzWebPushBundle']]);
    }
}
