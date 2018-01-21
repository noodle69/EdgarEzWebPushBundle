<?php

namespace Edgar\EzWebPushBundle\EventListener;

use Edgar\EzUIProfile\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ProfileConfigureMenuListener implements TranslationContainerInterface
{
    const ITEM_PROFILE_WEBPUSH = 'profile__webpush';

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
                'extras' => ['icon' => 'pin'],
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_PROFILE_WEBPUSH, 'messages'))->setDesc('My notifications'),
        ];
    }
}
