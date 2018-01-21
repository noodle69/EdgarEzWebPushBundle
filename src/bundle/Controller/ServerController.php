<?php

namespace Edgar\EzWebPushBundle\Controller;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServerController extends Controller
{
    /** @var array  */
    private $manifest;

    public function __construct(
        array $manifest
    ) {
        $this->manifest = $manifest;
    }

    public function manifestAction(): Response
    {
        $manifest = [
            'name' => $this->manifest['name'],
            'short_name' => $this->manifest['short_name'],
            'start_url' => '/',
            'display' => 'standalone',
            'key' => $this->manifest['api_key'],
            'gcm_sender_id' => $this->manifest['gcm_sender_id'],
            'gcm_user_visible_only' => true,
        ];

        $response = new JsonResponse($manifest);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
