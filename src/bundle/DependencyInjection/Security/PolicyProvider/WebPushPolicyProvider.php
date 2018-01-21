<?php

namespace Edgar\EzWebPushBundle\DependencyInjection\Security\PolicyProvider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class WebPushPolicyProvider extends YamlPolicyProvider
{
    /** @var string $path bundle path */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getFiles()
    {
        return [$this->path . '/Resources/config/policies.yml'];
    }
}
