<?php

namespace App\Form;

// https://symfony.com/doc/current/form/events.html

use App\Entity\City;
use App\Entity\Region;
use App\Repository\RegionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegionFieldSubscriber implements EventSubscriberInterface
{
    const REGION = 'region';
    private FormFactoryInterface $formFactory;
    private RegionRepository $regionRepository;

    public function __construct(FormFactoryInterface $formFactory, RegionRepository $regionRepository)
    {
        $this->formFactory = $formFactory;
        $this->regionRepository = $regionRepository;
    }

    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData', FormEvents::PRE_SUBMIT => 'preSubmit'];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $this->addCityField($form, $data->getRegion());
    }

    // You have to use preSubmit, as you're not allowed
    // to change the form any later
    public function preSubmit(FormEvent $formEvent)
    {
        $eventData = $formEvent->getData();


        $region = !array_key_exists(self::REGION, $eventData)
            ? null
            : $this->regionRepository->find($eventData[self::REGION]);

        $this->addCityField($formEvent->getForm(), $region);
    }

    private function addCityField(FormInterface $form, ?Region $region)
    {
        $cities = null === $region ? [] : $region->getCities();

        $form->add(
            'city',
            EntityType::class,
            [
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control selectpicker', 'data-live-search' => 'true'],
                'choices' => $cities,
                'constraints' => [new NotBlank()]
            ]
        );
    }
}
