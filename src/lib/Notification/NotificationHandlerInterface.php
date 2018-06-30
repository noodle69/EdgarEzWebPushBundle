<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPush\Notification;

use Edgar\EzWebPush\Model\Message\Notification;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;

interface NotificationHandlerInterface
{
    public function sendMessage(array $auth, EdgarEzWebPushEndpoint $webPushEndpoint, Notification $notification, bool $flush = false);

    public function flush();
}
