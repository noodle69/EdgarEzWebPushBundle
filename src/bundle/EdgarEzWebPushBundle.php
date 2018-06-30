<?php

namespace Edgar\EzWebPushBundle;

use Edgar\EzWebPushBundle\DependencyInjection\Security\PolicyProvider\WebPushPolicyProvider;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EdgarEzWebPushBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var EzPublishCoreExtension $eZExtension */
        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addPolicyProvider(new WebPushPolicyProvider($this->getPath()));
    }
}
