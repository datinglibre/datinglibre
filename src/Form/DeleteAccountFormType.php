<?php

declare(strict_types=1);

namespace App\Form;

use App\Repository\CountryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteAccountFormType extends AbstractType
{
    public function __construct(CountryRepository $countryRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function buildForm(FormBuilderInterface $deleteAccountForm, array $options)
    {
        $deleteAccountForm->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options'  => ['label' => 'user.password'],
            'second_options' => ['label' => 'user.confirm_password'],
        ]);

        $deleteAccountForm->add('submit', SubmitType::class, ['label' => 'account.delete_confirm']);
    }
}
