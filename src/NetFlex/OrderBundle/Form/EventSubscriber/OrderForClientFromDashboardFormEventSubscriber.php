<?php

namespace NetFlex\OrderBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderForClientFromDashboardFormEventSubscriber implements EventSubscriberInterface
{
	private $em;
	private $request;
	
	public function __construct(EntityManager $em, Request $request)
	{
		$this->em = $em;
		$this->request = $request;
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
		
		$deliveryCharge = $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findOneById($formData['deliveryChargeId']);
		
		$form->remove('deliveryChargeId');
		$form->add('deliveryChargeId', null, [
			'data' => $deliveryCharge,
		]);
	}
}
