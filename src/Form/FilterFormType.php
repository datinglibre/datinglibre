<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Region;
use App\Entity\Filter;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    private CountryRepository $countryRepository;
    private array $distances;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->distances = ['100' => '100000',
            '75' => '75000',
            '50' => '50000',
            '25' => '25000'];

        $this->countryRepository = $countryRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Filter::class, 'regions' => []]);
    }

    public function buildForm(FormBuilderInterface $searchFormBuilder, array $options)
    {
        $searchFormBuilder->add('region', EntityType::class, [
            'choices' => $options['regions'],
            'class' => Region::class,
            'choice_label' => 'name',
            'placeholder' => '',
            'required' => false
        ]);

        $searchFormBuilder->add('distance', ChoiceType::class, [
            'choices' => $this->distances,
            'choice_label' => function ($choice) {
                return $choice/1000 . " km";
            },
            'label' => 'search.distance',
            'placeholder' => '',
            'required' => false
        ]);

        $searchFormBuilder->add('min_age', ChoiceType::class, [
            'choices' => range(18, 100),
            'label' => 'search.minimum_age',
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => true
         ]);

        $searchFormBuilder->add('max_age', ChoiceType::class, [
            'choices' => range(18, 100),
            'label' => 'search.maximum_age',
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => true
        ]);

        $searchFormBuilder->add('save', SubmitType::class, [
            'attr' => ['id' => 'save'],
            'label' => 'search.filter'
        ]);
    }
}
