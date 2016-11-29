<?php

namespace NetFlex\OrderBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderForClientFromDashboardFormEventSubscriber implements EventSubscriberInterface
{
	private $em;
	private $request;
	private $clientOtherPickupAddresses;
	private $clientOtherBillingAddresses;
	
	public function __construct(EntityManager $em, Request $request, $clientOtherPickupAddresses, $clientOtherBillingAddresses)
	{
		$this->em = $em;
		$this->request = $request;
		$this->clientOtherPickupAddresses = $clientOtherPickupAddresses;
		$this->clientOtherBillingAddresses = $clientOtherBillingAddresses;
	}
	
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}
	
	public function preSetData(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		$clientPreferredPickupAddress = ($this->clientOtherPickupAddresses) ? array_values($this->clientOtherPickupAddresses)[0] : null;
		$clientPreferredBillingAddress = ($this->clientOtherBillingAddresses) ? array_values($this->clientOtherBillingAddresses)[0] : null;
		
		$form->add('clientOtherPickupAddresses', ChoiceType::class, [
			'placeholder' => '-Select A Pickup Address-',
			'choices' => $this->clientOtherPickupAddresses,
			'data' => $clientPreferredPickupAddress,
			'mapped' => false,
			'error_bubbling' => false,
		]);
		$form->add('clientOtherBillingAddresses', ChoiceType::class, [
			'placeholder' => '-Select A Billing Address-',
			'choices' => $this->clientOtherBillingAddresses,
			'data' => $clientPreferredBillingAddress,
			'mapped' => false,
			'error_bubbling' => false,
		]);
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		$form->remove('clientOtherPickupAddresses');
		$form->remove('clientOtherBillingAddresses');
		$form->add('deliveryChargeId', null, [
			'data' => $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findOneById($formData['deliveryChargeId']),
		]);
	}
}
