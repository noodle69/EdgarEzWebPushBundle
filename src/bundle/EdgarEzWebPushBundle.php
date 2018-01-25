<?php

namespace Edgar\EzWebPushBundle;

use Edgar\EzWebPushBundle\DependencyInjection\Security\PolicyProvider\WebPushPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EdgarEzWebPushBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
    }
}
