<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WebPushSWController extends Controller
{
    public function swAction(): Response
    {
        $response = new Response();
        $path = $this->get('kernel')->getRootDir() . '/../web/bundles/edgarezwebpush/js/service-worker.js';
        $response->setContent(file_get_contents($path));
        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }
}
