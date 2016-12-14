<?php

namespace NetFlex\UserBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressFormAddEventSubscriber implements EventSubscriberInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
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
		
		$form->add('countryId', null, [
			'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => $formData['countryId'], 'status' => 1])
		])
		->add('stateId', null, [
			'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['stateId'], 'status' => 1])
		])
		->add('cityId', null, [
			'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData['cityId'], 'status' => 1])
		]);
	}
}
