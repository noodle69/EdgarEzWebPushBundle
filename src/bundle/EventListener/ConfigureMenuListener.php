<?php

namespace Edgar\EzWebPushBundle\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use JMS\TranslationBundle\Model\Message;

class ConfigureMenuListener implements TranslationContainerInterface
{
    const ITEM_WEBPUSH = 'content__sidebar_right__webpush';

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM_WEBPUSH,
            [
                'extras' => ['icon' => 'subscriber'],
                'attributes' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#webpush-location-modal',
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
            (new Message(self::ITEM_WEBPUSH, 'messages'))->setDesc('Notify'),
        ];
    }
}
