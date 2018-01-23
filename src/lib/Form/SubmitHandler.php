<?php

namespace Edgar\EzWebPush\Form;

use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

class SubmitHandler
{
    /** @var NotificationHandlerInterface $notificationHandler */
    protected $notificationHandler;

    /** @var RouterInterface $router */
    protected $router;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param RouterInterface $router
     */
    public function __construct(NotificationHandlerInterface $notificationHandler, RouterInterface $router)
    {
        $this->notificationHandler = $notificationHandler;
        $this->router = $router;
    }

    /**
     * Wraps business logic with reusable boilerplate code.
     *
     * Handles form errors (NotificationHandler:warning).
     * Handles business logic exceptions (NotificationHandler:error).
     *
     * @param FormInterface $form
     * @param callable(mixed):array $handler
     *
     * @return null|Response
     */
    public function handle(FormInterface $form, APIUser $user, callable $handler): ?Response
    {
        $data = $form->getData();

        if ($form->isValid()) {
            try {
                $result = $handler($data);

                if ($result instanceof Response) {
                    return $result;
                }
            } catch (\Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        } else {
            foreach ($form->getErrors(true, true) as $formError) {
                $this->notificationHandler->warning($formError->getMessage());
            }
        }

        return null;
    }
}
