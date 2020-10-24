<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => PasswordUpdateForm::class]);
    }

    public function buildForm(FormBuilderInterface $passwordUpdateFormBuilder, array $options)
    {
        $passwordUpdateFormBuilder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'user.password'],
            'second_options' => ['label' => 'user.confirm_password']
        ]);

        $passwordUpdateFormBuilder->add('secret', HiddenType::class, ['required' => true]);
        $passwordUpdateFormBuilder->add('userId', HiddenType::class, ['required' => true]);
        $passwordUpdateFormBuilder->add('submit', SubmitType::class);
    }
}
