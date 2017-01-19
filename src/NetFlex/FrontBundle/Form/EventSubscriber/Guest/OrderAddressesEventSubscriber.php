<?php

namespace NetFlex\FrontBundle\Form\EventSubscriber\Guest;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderAddressesEventSubscriber implements EventSubscriberInterface
{
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
        
        $form->add('shippingCityId', null, [
            'data' => $formData['shippingCityId'],
        ]);
	}
}
