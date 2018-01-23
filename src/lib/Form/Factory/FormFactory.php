<?php
/**
 * Created by PhpStorm.
 * User: Emmanuel
 * Date: 23/01/2018
 * Time: 22:12
 */

namespace Edgar\EzWebPush\Form\Factory;

use Edgar\EzWebPush\Data\EdgarEzWebPushMessage;
use Edgar\EzWebPush\Form\Type\MessageType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactory
{
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function sendMessage(
        EdgarEzWebPushMessage $data,
        ?string $name = null
    ): ?FormInterface {
        $name = $name ?: 'message-send';

        return $this->formFactory->createNamed(
            $name,
            MessageType::class,
            $data,
            [
                'method' => Request::METHOD_POST,
                'csrf_protection' => true,
            ]
        );
    }
}
