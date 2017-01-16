<?php

namespace NetFlex\DeliveryChargeBundle\Form\EventSubscriber\DeliveryCharge;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DeliveryChargeEditTypeEventSubscriber implements EventSubscriberInterface
{
	private $em;
    private $deliveryZone;
    
    public function __construct(EntityManager $em, $deliveryZone)
    {
        $this->em = $em;
        $this->deliveryZone = $deliveryZone;
    }
    
    public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
        
        $form->add('sourceStateId', EntityType::class, [
            'placeholder' => '-Select A Source State-',
            'class' => 'NetFlexLocationBundle:State',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['sourceCountryId']) {
                    return $er->createQueryBuilder('STATE')
                        ->where('STATE.countryId = ' . $formData['sourceCountryId'])
                        ->andWhere('STATE.id not in (42, 43, 44, 45, 46, 47)')
                        ->andWhere('STATE.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['sourceStateId']) ? $this->em->getReference('NetFlexLocationBundle:State', ['id' =>
                $formData['sourceStateId'], 'status' => 1]) : null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Source state is required',
                ]),
            ],
        ])
        ->add('sourceCityId', EntityType::class, [
            'placeholder' => '-Select A Source City-',
            'class' => 'NetFlexLocationBundle:City',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['sourceStateId']) {
                    return $er->createQueryBuilder('CITY')
                        ->where('CITY.stateId = ' . $formData['sourceStateId'])
                        ->where('CITY.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['sourceCityId']) ? $this->em->getReference('NetFlexLocationBundle:City', ['id' =>
                $formData['sourceCityId'], 'status' => 1]) : null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Source city is required',
                ]),
            ],
        ])
        ->add('destinationCountryId', EntityType::class, [
            'placeholder' => '-Select A Destination City-',
            'class' => 'NetFlexLocationBundle:Country',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['destinationCountryId']) {
                    return $er->createQueryBuilder('COUNTRY')
                        ->where('COUNTRY.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['destinationCountryId']) ? $this->em->getReference('NetFlexLocationBundle:Country', [ 'id' => $formData['destinationCountryId'], 'status' => 1]) : null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination country is required',
                ]),
            ],
        ])
        ->add('destinationStateId', EntityType::class, [
            'placeholder' => '-Select A Destination State-',
            'class' => 'NetFlexLocationBundle:State',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['destinationCountryId']) {
                    return $er->createQueryBuilder('STATE')
                        ->where('STATE.countryId = ' . $formData['destinationCountryId'])
                        ->andWhere('STATE.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['destinationStateId']) ? $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['destinationStateId'], 'status' => 1]) : null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination state is required',
                ]),
            ],
        ])
        ->add('destinationCityId', EntityType::class, [
            'placeholder' => '-Select A Destination City-',
            'class' => 'NetFlexLocationBundle:City',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['destinationStateId']) {
                    return $er->createQueryBuilder('CITY')
                        ->where('CITY.stateId = ' . $formData['destinationStateId'])
                        ->where('CITY.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['destinationCityId']) ? $this->em->getReference('NetFlexLocationBundle:City', ['id' =>
                $formData['destinationCityId'], 'status' => 1]) : null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination city is required',
                ]),
            ],
        ]);
        
        if (1 != $this->deliveryZone) {
            if ($form->has('sourceZipCodeRange')) {
                $form->remove('sourceZipCodeRange');
            }
            
            if ($form->has('destinationZipCodeRange')) {
                $form->remove('destinationZipCodeRange');
            }
        }
	}
}
