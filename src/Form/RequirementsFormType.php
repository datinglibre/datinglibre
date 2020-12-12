<?php

declare(strict_types=1);

namespace App\Form;

use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementsFormType extends AbstractType
{
    private CategoryRepository $categoryRepository;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => RequirementsForm::class]);
    }

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $requirementsFormBuilder, array $options)
    {
        $requirementsFormBuilder->add(
            'sexes',
            ChoiceType::class,
            [
                    'label' => 'sex.search',
                    'choices' => $this->categoryRepository->findOneBy(['name' => 'sex'])->getAttributes(),
                    'choice_label' => 'name',
                    'choice_translation_domain' => 'attributes',
                    'multiple' => true,
                    'expanded' => true
            ]
        );

        $requirementsFormBuilder->add('save', SubmitType::class, [
            'label' => 'search.search'
        ]);
    }
}
