<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Country;
use App\Entity\Region;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CountryFieldSubscriber implements EventSubscriberInterface
{
    const COUNTRY = 'country';
    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData', FormEvents::PRE_SUBMIT => 'preSubmit'];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $this->addRegionField($form, $data->getCountry());
    }

    public function preSubmit(PreSubmitEvent $event)
    {
        $form = $event->getForm();
        $eventData = $event->getData();

        $country = !array_key_exists(self::COUNTRY, $eventData)
            ? null
            : $this->countryRepository->find($eventData[self::COUNTRY]);

        $this->addRegionField($form, $country);
    }

    private function addRegionField(FormInterface $form, ?Country $country)
    {
        $regions = null === $country ? [] : $country->getRegions();

        $form->add(
            'region',
            EntityType::class,
            [
                'attr' => ['class' => 'form-control selectpicker', 'data-live-search' => 'true'],
                'class' => Region::class,
                'choice_label' => 'name',
                'choices' => $regions,
                'constraints' => [new NotBlank()]
            ]
        );
    }
}
