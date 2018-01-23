<?php

namespace Edgar\EzWebPush\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_identifier', TextType::class, [
                'required' => true,
            ])
            ->add('message', TextType::class, [
                'required' => true,
            ])
            ->add('locationId', HiddenType::class, [
                'required' => true,
            ])
            ->add('send', SubmitType::class, [
                'label' => /** @Desc("Send") */ 'message_form.send'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([]);
    }
}
