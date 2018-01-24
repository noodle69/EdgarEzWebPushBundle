<?php

namespace Edgar\EzWebPush\Notification;

use Edgar\EzWebPush\Model\Message\Notification;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;
use Edgar\EzWebPushBundle\Exception\WebPushException;
use Minishlink\WebPush\WebPush;

class WebPushNotificationHandler implements NotificationHandlerInterface
{
    /** @var WebPush  */
    private $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush();
    }

    /**
     * @param array $auth
     * @param EdgarEzWebPushEndpoint $webPushEndpoint
     * @param Notification $notification
     * @param bool $flush
     * @throws WebPushException
     */
    public function sendMessage(array $auth, EdgarEzWebPushEndpoint $webPushEndpoint, Notification $notification, bool $flush = false)
    {
        $this->webPush = new WebPush($auth);

        try {
            $this->webPush->sendNotification(
                $webPushEndpoint->getEndpoint(),
                $notification,
                $webPushEndpoint->getPublicKey(),
                $webPushEndpoint->getAuthToken()
            );
        } catch (\ErrorException $e) {
            throw new WebPushException($e->getMessage());
        }
    }

    public function flush()
    {
        $this->webPush->flush();
    }
}
