<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SWListener implements EventSubscriberInterface
{
    /**
     * The fragment path (for ESI/Hinclude...).
     *
     * @var string
     */
    protected $fragmentPath;

    public function __construct(
        $fragmentPath = '/_fragment'
    ) {
        $this->fragmentPath = $fragmentPath;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        // Ignore sub-requests, including fragments.
        if (!$this->isMasterRequest($request, $event->getRequestType())) {
            return;
        }

        if ($request->attributes->get('_route') === 'edgar.ezwebpush.sw') {
            $request->attributes->set('siteaccess', false);

            return;
        }
    }

    private function isMasterRequest(Request $request, $requestType)
    {
        if (
            $requestType !== HttpKernelInterface::MASTER_REQUEST
            || substr($request->getPathInfo(), -strlen($this->fragmentPath)) === $this->fragmentPath
        ) {
            return false;
        }

        return true;
    }
}
