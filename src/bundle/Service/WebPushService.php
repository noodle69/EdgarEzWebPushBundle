<?php

namespace Edgar\EzWebPushBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzWebPush\Model\Message\Notification;
use Edgar\EzWebPush\Notification\NotificationHandlerInterface;
use Edgar\EzWebPush\Repository\EdgarEzWebPushEndpointRepository;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;
use Edgar\EzWebPushBundle\Exception\WebPushException;
use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\VariationHandler;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\SPI\Variation\Values\Variation;

class WebPushService
{
    /** @var array  */
    private $auth;

    /** @var EdgarEzWebPushEndpointRepository  */
    private $webPushRepository;

    /** @var UserService  */
    private $userService;

    /** @var LocationService  */
    private $locationService;

    /** @var NotificationHandlerInterface  */
    private $notificationHandler;

    /** @var TranslatorInterface  */
    private $translator;

    /** @var VariationHandler  */
    private $imageVariationService;

    /** @var Packages  */
    private $packages;

    /** @var UrlGeneratorInterface  */
    private $generator;

    public function __construct(
        string $subject,
        string $privateKey,
        string $publicKey,
        Registry $doctrineRegistry,
        UserService $userService,
        LocationService $locationService,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        VariationHandler $imageVariationService,
        Packages $packages,
        UrlGeneratorInterface $generator
    ) {
        $this->auth = [
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey
            ],
        ];

        $entityManager = $doctrineRegistry->getManager();
        $this->webPushRepository = $entityManager->getRepository(EdgarEzWebPushEndpoint::class);

        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->imageVariationService = $imageVariationService;
        $this->packages = $packages;
        $this->generator = $generator;
    }

    /**
     * @param int $fromUserId
     * @param int $toUserId
     * @param string $title
     * @param string $message
     * @throws WebPushException
     */
    public function sendLocationNotificationToUser(
        int $fromUserId,
        int $toUserId,
        string $title,
        string $message,
        int $locationId
    ) {
        $webPushEndpoints = $this->webPushRepository->findBy(['userId' => $toUserId]);
        if (!$webPushEndpoints || count($webPushEndpoints) == 0) {
            throw new WebPushException(
                $this->translator->trans(
                    'edgar.ezwebpush.exception.contact_no_subscribed',
                    [],
                    'edgarezwebpush'
                )
            );
        }

        try {
            /** @var User $fromUser */
            $fromUser = $this->getUser($fromUserId);
        } catch (WebPushException $e) {
            throw new WebPushException($e->getMessage());
        }

        $location = $this->locationService->loadLocation($locationId);
        $url = $this->generator->generate('ez_urlalias', ['locationId' => $location->id], UrlGeneratorInterface::ABSOLUTE_URL);

        $notification = new Notification([
            'title' => $title,
            'body'  => $message,
            'icon'  => $this->getUserAvatar($fromUser),
            'data' => [
                'url' => $url,
            ]
        ]);

        foreach ($webPushEndpoints as $webPushEndpoint) {
            try {
                $this->notificationHandler->sendMessage($this->auth, $webPushEndpoint, $notification);
            } catch (WebPushException $e) {
                throw new WebPushException($e->getMessage());
            }
        }

        $this->notificationHandler->flush();
    }

    /**
     * @param int $userId
     * @return User
     * @throws WebPushException
     */
    public function getUser(int $userId): User
    {
        try {
            $user = $this->userService->loadUser($userId);
            return $user;
        } catch (NotFoundException $e) {
            throw new WebPushException(
                $this->translator->trans(
                    'edgar.ezwebpush.exception.no_contact',
                    [],
                    'edgarezwebpush'
                )
            );
        }
    }

    /**
     * @param string $userLogin
     * @return User
     * @throws WebPushException
     */
    public function getUserByLogin(string $userLogin): User
    {
        try {
            $user = $this->userService->loadUserByLogin($userLogin);
            return $user;
        } catch (NotFoundException $e) {
            throw new WebPushException(
                $this->translator->trans(
                    'edgar.ezwebpush.exception.no_contact',
                    [],
                    'edgarezwebpush'
                )
            );
        }
    }

    /**
     * @param int|null $locationId
     * @return bool
     * @throws WebPushException
     */
    public function hasLocationAccess(?int $locationId): bool
    {
        if (!$locationId) {
            throw new WebPushException(
                $this->translator->trans(
                    'edgar.ezwebpush.exception.no_location',
                    [],
                    'edgarezwebpush'
                )
            );
        }

        try {
            $this->locationService->loadLocation($locationId);
        } catch (UnauthorizedException | NotFoundException $e) {
            throw new WebPushException(
                $this->translator->trans(
                    'edgar.ezwebpush.exception.location : %message%',
                    ['message' => $e->getMessage()],
                    'edgarezwebpush'
                )
            );
        }

        return true;
    }

    private function getUserAvatar(User $user, string $variationName = 'ezplatform_admin_ui_profile_picture_user_menu'): string
    {
        $field = $user->getField('image');
        $versionInfo = $user->getVersionInfo();

        try {
            /** @var Variation $variation */
            $variation = $this->imageVariationService->getVariation($field, $versionInfo, $variationName);

            $imageUri = $variation ? $variation->uri : 'bundles/ezplatformadminui/img/default-profile-picture.png';
            return $this->packages->getUrl($imageUri);
        } catch (InvalidVariationException $e) {
            if (isset($this->logger)) {
                $this->logger->error("Couldn't get variation '{$variationName}' for image with id {$field->value->id}");
            }
        } catch (SourceImageNotFoundException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' for image with id {$field->value->id} because source image can't be found"
                );
            }
        } catch (\InvalidArgumentException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' for image with id {$field->value->id} because an image could not be created from the given input"
                );
            }
        }

        $imageUri = 'bundles/ezplatformadminui/img/default-profile-picture.png';
        return $this->packages->getUrl($imageUri);
    }
}
