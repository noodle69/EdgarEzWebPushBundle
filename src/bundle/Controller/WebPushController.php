<?php

namespace Edgar\EzWebPushBundle\Controller;

use Edgar\EzWebPush\Data\EdgarEzWebPushMessage;
use Edgar\EzWebPush\Form\Factory\FormFactory;
use Edgar\EzWebPush\Form\SubmitHandler;
use Edgar\EzWebPush\Model\Message\Notification;
use Edgar\EzWebPushBundle\Service\WebPushService;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Minishlink\WebPush\WebPush;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

class WebPushController extends Controller
{
    /** @var FormFactory  */
    protected $formFactory;

    /** @var SubmitHandler  */
    protected $submitHandler;

    /** @var \Doctrine\Common\Persistence\ObjectRepository  */
    private $webPushRepository;

    /** @var TokenStorage $tokenStorage */
    private $tokenStorage;

    /** @var RouterInterface  */
    private $router;

    /** @var WebPushService  */
    private $webPushService;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        Registry $doctrineRegistry,
        TokenStorage $tokenStorage,
        RouterInterface $router,
        WebPushService $webPushService
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->webPushService = $webPushService;

        $entityManager = $doctrineRegistry->getManager();
        $this->webPushRepository = $entityManager->getRepository(EdgarEzWebPushEndpoint::class);
    }

    public function profileAction(): Response
    {
        return $this->render('@EdgarEzWebPush/profile/webpush.html.twig', [
            'vapid_public_key' => $this->container->getParameter('edgar_ez_web_push.vapid_public_key'),
        ]);
    }

    public function swAction(): Response
    {
        $response = new Response();
        $path = $this->get('kernel')->getRootDir() . '/../web/bundles/edgarezwebpush/js/service-worker.js';
        $response->setContent(file_get_contents($path));
        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }

    public function registerAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $content = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $response = new JsonResponse(['success' => false]);
            return $response;
        }

        if (!isset($content['endpoint'])
            || !isset($content['keys']['auth'])
            || !isset($content['keys']['p256dh'])
        ) {
            $response = new JsonResponse(['success' => false]);
            return $response;
        }

        if (!$this->webPushRepository->save($apiUser->id, $content['endpoint'], $content['keys']['auth'], $content['keys']['p256dh'])) {
            $response = new JsonResponse(['success' => false]);
            return $response;
        }

        $response = new JsonResponse(['success' => true]);
        return $response;
    }

    public function unregisterAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $content = json_decode($request->getContent());
        if (!isset($content->endpoint)) {
            $response = new JsonResponse(['success' => false]);
            return $response;
        }

        if (!$this->webPushRepository->delete($apiUser->id, $content->endpoint)) {
            $response = new JsonResponse(['success' => false]);
            return $response;
        }

        $response = new JsonResponse(['success' => true]);
        return $response;
    }

    public function testAction(): Response
    {
        $webPushEndpoints = $this->webPushRepository->findAll();

        if (!$webPushEndpoints || count($webPushEndpoints) == 0) {
            return new RedirectResponse($this->router->generate('edgar.ezuibookmark.profile'));
        }

        $auth = [
            'VAPID' => [
                'subject' => 'ezplatform.local',
                'publicKey' => 'BKNY4s9LGtKS5xhQSDSffrCqWe2htqggyGMJHtSP4Yh4kdBSreiNfL8u+a4Uj2W5as0YPNdrGoSIoezBlPNpZRw=',
                'privateKey' => '2jTTbzmqWVSxMzpKa1iUNqyVcpPJ33/M08DK9/SFzpQ=',
            ],
        ];

        $webPush = new WebPush($auth);

        $notification = new Notification([
            'title' => 'Awesome title',
            'body'  => 'Symfony is great!',
            'icon'  => 'https://symfony.com/logos/symfony_black_03.png',
            'data'  => [
                'link' => 'https://www.symfony.com',
            ],
        ]);

        foreach ($webPushEndpoints as $webPushEndpoint) {
            try {
                $res = $webPush->sendNotification(
                    $webPushEndpoint->getEndpoint(),
                    $notification,
                    $webPushEndpoint->getPublicKey(),
                    $webPushEndpoint->getAuthToken()
                );
            } catch (\ErrorException $e) {
            }
        }

        $webPush->flush();

        return new RedirectResponse($this->router->generate('edgar.ezwebpush.profile'));
    }

    public function modalAction(): Response
    {
        $formMessage = $this->formFactory->sendMessage(new EdgarEzWebPushMessage());

        return $this->render('@EdgarEzWebPush/webpush/modal_webpush.html.twig', [
            'form_message' => $formMessage->createView()
        ]);
    }

    public function messageAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $form = $this->formFactory->sendMessage(
            new EdgarEzWebPushMessage()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, $apiUser, function (EdgarEzWebPushMessage $data, APIUser $apiUser) {


                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $data->getLocationId(),
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $locationId = $request->request->get('location_id', null);
        try {
            $this->webPushService->hasLocationAccess($locationId);
            return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                'locationId' => $locationId,
            ]));
        } catch (WebPushException $e) {
            $this->notificationHandler->error(
                $e->getMessage()
            );
            return new RedirectResponse($this->generateUrl('ezplatform.dashboard', []));
        }
    }
}
