<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPush\Notification;

use Edgar\EzWebPush\Model\Message\Notification;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;
use Edgar\EzWebPushBundle\Exception\WebPushException;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotificationHandler implements NotificationHandlerInterface
{
    /** @var WebPush */
    private $webPush;

    /**
     * WebPushNotificationHandler constructor.
     */
    public function __construct()
    {
        try {
            $this->webPush = new WebPush();
        } catch (\ErrorException $e) {
            $this->webPush = false;
        }
    }

    /**
     * @param array $auth
     * @param EdgarEzWebPushEndpoint $webPushEndpoint
     * @param Notification $notification
     * @param bool $flush
     *
     * @throws WebPushException
     */
    public function sendMessage(array $auth, EdgarEzWebPushEndpoint $webPushEndpoint, Notification $notification, bool $flush = false)
    {
        try {
            $this->webPush = new WebPush($auth);

            $subscription = new Subscription(
                $webPushEndpoint->getEndpoint(),
                $webPushEndpoint->getPublicKey(),
                $webPushEndpoint->getAuthToken()
            );

            $this->webPush->sendNotification(
                $subscription,
                $notification
            );
        } catch (\ErrorException $e) {
            throw new WebPushException($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function flush()
    {
        try {
            return $this->webPush->flush();
        } catch (\ErrorException $e) {
            return false;
        }
    }
}
