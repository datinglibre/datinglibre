<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModerateFormType extends AbstractType
{
    public function __construct()
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => ModerateForm::class]);
    }

    public function buildForm(FormBuilderInterface $moderateForm, array $options)
    {
        $moderateForm->add('id', HiddenType::class);
        $moderateForm->add('accept', SubmitType::class, ['label' => 'moderate.accept']);
        $moderateForm->add('reject', SubmitType::class, ['label' => 'moderate.reject']);
    }
}
