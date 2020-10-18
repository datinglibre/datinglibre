<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFormType extends AbstractType
{
    public function __construct()
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => MessageForm::class]);
    }

    public function buildForm(FormBuilderInterface $messageForm, array $options)
    {
        $messageForm->add('content', TextareaType::class, ['label' => 'message.message']);
        $messageForm->add('submit', SubmitType::class, ['label' => 'message.send']);
    }
}
