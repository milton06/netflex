<?php

namespace NetFlex\DeliveryChargeBundle\Form\EventSubscriber\DeliveryCharge;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeliveryChargeSearchTypeEventSubscriber implements EventSubscriberInterface
{
	private $options;
    
    public function __construct($options)
    {
        $this->options = $options;
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
		
		var_dump($formData);
        
        $form->add('sourceCountryId', null, [
            'data' => ($formData['sourceCountryId']) ? $formData['sourceCountryId'] : null,
        ])
        ->add('sourceStateId', null, [
            'data' => ($formData['sourceStateId']) ? $formData['sourceStateId'] : null,
        ])
        ->add('sourceCityId', null, [
            'data' => ($formData['sourceCityId']) ? $formData['sourceCityId'] : null,
        ])
        ->add('destinationCountryId', null, [
            'data' => ($formData['destinationCountryId']) ? $formData['destinationCountryId'] : null,
        ])
        ->add('destinationStateId', null, [
            'data' => ($formData['destinationStateId']) ? $formData['destinationStateId'] : null,
        ])
        ->add('destinationCityId', null, [
            'data' => ($formData['destinationCityId']) ? $formData['destinationCityId'] : null,
        ]);
	}
}
