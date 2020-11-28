<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function buildForm(FormBuilderInterface $passwordResetFormBuilder, array $options)
    {
        $passwordResetFormBuilder->add('email', EmailType::class, [
            'required' => true,
            'constraints' => [
                new Email([
                    'mode' => 'html5',
                    'normalizer' => 'trim',
                    'message' => 'reset_password.invalid_email'
                ]),
                new NotBlank([
                    'message' => 'reset_password.blank_email'
                ])
            ]
        ]);
    }
}
