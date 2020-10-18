<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Attribute;
use App\Entity\Country;
use App\Repository\CategoryRepository;
use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    private CategoryRepository $categoryRepository;
    private CountryRepository $countryRepository;
    private RegionRepository $regionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        CountryRepository $countryRepository,
        RegionRepository $regionRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;
        $this->regionRepository = $regionRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => ProfileForm::class]);
    }


    public function buildForm(FormBuilderInterface $profileFormBuilder, array $options)
    {
        $categories = $this->categoryRepository->findAll();
        $countries = $this->countryRepository->findAll();

        foreach ($categories as $category) {
            $profileFormBuilder->add(
                $category->getLowercaseName(),
                EntityType::class,
                [
                    'choices' => $category->getAttributes(),
                    'class' => Attribute::class,
                    'choice_label' => 'name',
                ]
            );
        }

        $profileFormBuilder->add(
            'username',
            TextType::class,
            [
                'label' => 'profile.username'
            ]
        );

        $profileFormBuilder->add(
            'dob',
            DateType::class,
            [
                'years' => range(date('Y')-100, date('Y')-18),
                'label' => 'profile.dob'
            ]
        );

        $profileFormBuilder->add(
            'country',
            EntityType::class,
            [
                'attr' => ['class' => 'form-control selectpicker', 'data-live-search' => 'true'],
                'choices' => $countries,
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => ''
            ]
        );


        $profileFormBuilder->addEventSubscriber(new CountryFieldSubscriber($this->countryRepository));
        $profileFormBuilder->addEventSubscriber(new RegionFieldSubscriber(
            $profileFormBuilder->getFormFactory(),
            $this->regionRepository
        ));

        $profileFormBuilder->add(
            'about',
            TextareaType::class,
            [
                'attr' => ['rows' => 5],
                'required' => false,
                'label' => 'profile.about'
            ]
        );

        $profileFormBuilder->add('save', SubmitType::class, [
            'attr' => ['id' => 'save'],
            'label' => 'controls.save'
        ]);
    }
}
