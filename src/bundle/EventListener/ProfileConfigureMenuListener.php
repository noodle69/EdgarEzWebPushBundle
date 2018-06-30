<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\EventListener;

use Edgar\EzUIProfile\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ProfileConfigureMenuListener implements TranslationContainerInterface
{
    const ITEM_PROFILE_WEBPUSH = 'profile__webpush';
    const ITEM_PROFILE_WEBPUSH_DESCRIPTION = 'profile__webpush_description';

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM_PROFILE_WEBPUSH,
            [
                'route' => 'edgar.ezwebpush.profile',
                'extras' => [
                    'icon' => 'subscriber',
                    'description' => self::ITEM_PROFILE_WEBPUSH_DESCRIPTION,
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_PROFILE_WEBPUSH, 'messages'))->setDesc('Notifications'),
            (new Message(self::ITEM_PROFILE_WEBPUSH_DESCRIPTION, 'messages'))->setDesc(
                'Activate WebPush to receive user or application browser notifications'
            ),
        ];
    }
}
