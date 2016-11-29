<?php

namespace NetFlex\OrderBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderAddressForClientFromDashboardFormEventSubscriber implements EventSubscriberInterface
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
		
		if ('edit_order' === $this->request->get('_route')) {
			$form->add('pickupFirstName', null, [
				'data' => (($formData) && ($formData->getPickupFirstName())) ? $formData->getPickupFirstName() : '',
			])
			->add('pickupMidName', null, [
				'data' => (($formData) && ($formData->getPickupMidName())) ? $formData->getPickupMidName() : '',
			])
			->add('pickupLastName', null, [
				'data' => (($formData) && ($formData->getPickupLastName())) ? $formData->getPickupLastName() : '',
			])
			->add('pickupAddressLine1', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine1())) ? $formData->getPickupAddressLine1() : '',
			])
			->add('pickupAddressLine2', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine2())) ? $formData->getPickupAddressLine2() : '',
			])
			->add('pickupCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => (($formData) && ($formData->getPickUpCountryId())) ? $formData->getPickUpCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
				'attr' => [
					'class' => 'country-selectors',
				],
			])
			->add('pickupStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getPickUpCountryId())) {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = ' . $formData->getPickUpCountryId()->getId())
							->orderBy('states.name', 'ASC');
					} else {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = 1')
							->orderBy('states.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getPickUpStateId())) ? $formData->getPickUpStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
				'attr' => [
					'class' => 'state-selectors',
				],
			])
			->add('pickupCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getPickUpStateId())) {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = ' . $formData->getPickUpStateId()->getId())
							->orderBy('cities.name', 'ASC');
					} else {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = 41')
							->orderBy('cities.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getPickUpCityId())) ? $formData->getPickUpCityId() : $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1]),
				'attr' => [
					'class' => 'city-selectors',
				],
			])
			->add('pickupZipCode', null, [
				'data' => (($formData) && ($formData->getPickupZipCode())) ? $formData->getPickupZipCode() : '',
			])
			->add('billingFirstName', null, [
				'data' => (($formData) && ($formData->getBillingFirstName())) ? $formData->getBillingFirstName() : '',
			])
			->add('billingMidName', null, [
				'data' => (($formData) && ($formData->getBillingMidName())) ? $formData->getBillingMidName() : '',
			])
			->add('billingLastName', null, [
				'data' => (($formData) && ($formData->getBillingLastName())) ? $formData->getBillingLastName() : '',
			])
			->add('billingAddressLine1', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine1())) ? $formData->getBillingAddressLine1() : '',
			])
			->add('billingAddressLine2', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine2())) ? $formData->getBillingAddressLine2() : '',
			])
			->add('billingCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => (($formData) && ($formData->getBillingCountryId())) ? $formData->getBillingCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
				'attr' => [
					'class' => 'country-selectors',
				],
			])
			->add('billingStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getBillingCountryId())) {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = ' . $formData->getBillingCountryId()->getId())
							->orderBy('states.name', 'ASC');
					} else {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = 1')
							->orderBy('states.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getBillingStateId())) ? $formData->getBillingStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
				'attr' => [
					'class' => 'state-selectors',
				],
			])
			->add('billingCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getBillingStateId())) {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = ' . $formData->getBillingStateId()->getId())
							->orderBy('cities.name', 'ASC');
					} else {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = 41')
							->orderBy('cities.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getBillingCityId())) ? $formData->getBillingCityId() : $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1]),
				'attr' => [
					'class' => 'city-selectors',
				],
			])
			->add('billingZipCode', null, [
				'data' => (($formData) && ($formData->getBillingZipCode())) ? $formData->getBillingZipCode() : '',
			])
			->add('shippingCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
				'attr' => [
					'class' => 'country-selectors',
				],
			])
			->add('shippingStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('states')
						->where('states.status = 1')
						->andWhere('states.countryId = 1')
						->orderBy('states.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
				'attr' => [
					'class' => 'state-selectors',
				],
			])
			->add('shippingCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('cities')
						->where('cities.status = 1')
						->andWhere('cities.stateId = 41')
						->orderBy('cities.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1]),
				'attr' => [
					'class' => 'city-selectors',
				],
			]);
		} else {
			$form->add('pickupFirstName', null, [
				'data' => (($formData) && ($formData->getPickupFirstName())) ? $formData->getPickupFirstName() : '',
			])
			->add('pickupMidName', null, [
				'data' => (($formData) && ($formData->getPickupMidName())) ? $formData->getPickupMidName() : '',
			])
			->add('pickupLastName', null, [
				'data' => (($formData) && ($formData->getPickupLastName())) ? $formData->getPickupLastName() : '',
			])
			->add('pickupAddressLine1', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine1())) ? $formData->getPickupAddressLine1() : '',
			])
			->add('pickupAddressLine2', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine2())) ? $formData->getPickupAddressLine2() : '',
			])
			->add('pickupCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => (($formData) && ($formData->getPickUpCountryId())) ? $formData->getPickUpCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
			])
			->add('pickupStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getPickUpCountryId())) {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = ' . $formData->getPickUpCountryId()->getId())
							->orderBy('states.name', 'ASC');
					} else {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = 1')
							->orderBy('states.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getPickUpStateId())) ? $formData->getPickUpStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
			])
			->add('pickupCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getPickUpStateId())) {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = ' . $formData->getPickUpStateId()->getId())
							->orderBy('cities.name', 'ASC');
					} else {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = 41')
							->orderBy('cities.name', 'ASC');
					}
				},
			])
			->add('pickupZipCode', null, [
				'data' => (($formData) && ($formData->getPickupZipCode())) ? $formData->getPickupZipCode() : '',
			])
			->add('pickupEmail', null, [
				'data' => (($formData) && ($formData->getPickupEmail())) ? $formData->getPickupEmail() : '',
			])
			->add('pickupContactNumber', null, [
				'data' => (($formData) && ($formData->getPickupContactNumber())) ? $formData->getPickupContactNumber() : '',
			])
			->add('billingFirstName', null, [
				'data' => (($formData) && ($formData->getBillingFirstName())) ? $formData->getBillingFirstName() : '',
			])
			->add('billingMidName', null, [
				'data' => (($formData) && ($formData->getBillingMidName())) ? $formData->getBillingMidName() : '',
			])
			->add('billingLastName', null, [
				'data' => (($formData) && ($formData->getBillingLastName())) ? $formData->getBillingLastName() : '',
			])
			->add('billingAddressLine1', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine1())) ? $formData->getBillingAddressLine1() : '',
			])
			->add('billingAddressLine2', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine2())) ? $formData->getBillingAddressLine2() : '',
			])
			->add('billingCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => (($formData) && ($formData->getBillingCountryId())) ? $formData->getBillingCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
			])
			->add('billingStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getBillingCountryId())) {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = ' . $formData->getBillingCountryId()->getId())
							->orderBy('states.name', 'ASC');
					} else {
						return $er->createQueryBuilder('states')
							->where('states.status = 1')
							->andWhere('states.countryId = 1')
							->orderBy('states.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getBillingStateId())) ? $formData->getBillingStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
			])
			->add('billingCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getBillingStateId())) {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = ' . $formData->getBillingStateId()->getId())
							->orderBy('cities.name', 'ASC');
					} else {
						return $er->createQueryBuilder('cities')
							->where('cities.status = 1')
							->andWhere('cities.stateId = 41')
							->orderBy('cities.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getBillingCityId())) ? $formData->getBillingCityId() : $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1]),
			])
			->add('billingZipCode', null, [
				'data' => (($formData) && ($formData->getBillingZipCode())) ? $formData->getBillingZipCode() : '',
			])
			->add('billingEmail', null, [
				'data' => (($formData) && ($formData->getBillingEmail())) ? $formData->getBillingEmail() : '',
			])
			->add('billingContactNumber', null, [
				'data' => (($formData) && ($formData->getBillingContactNumber())) ? $formData->getBillingContactNumber() : '',
			])
			->add('shippingCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
			])
			->add('shippingStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('states')
						->where('states.status = 1')
						->andWhere('states.countryId = 1')
						->orderBy('states.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
			])
			->add('shippingCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('cities')
						->where('cities.status = 1')
						->andWhere('cities.stateId = 41')
						->orderBy('cities.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1]),
			]);
		}
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		if ('edit_order' === $this->request->get('_route')) {
			//
		} else {
			$form->add('pickupFirstName', null, [
				'data' => $formData['pickupFirstName'],
			])
			->add('pickupMidName', null, [
				'data' => $formData['pickupMidName'],
			])
			->add('pickupLastName', null, [
				'data' => $formData['pickupLastName'],
			])
			->add('pickupAddressLine1', null, [
				'data' => $formData['pickupAddressLine1'],
			])
			->add('pickupAddressLine2', null, [
				'data' => $formData['pickupAddressLine2'],
			])
			->add('pickupCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => $formData['pickupCountryId'], 'status' => 1]),
			])
			->add('pickupStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('states')
						->where('states.status = 1')
						->andWhere('states.countryId = ' . $formData['pickupCountryId'])
						->orderBy('states.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['pickupStateId'], 'status' => 1]),
			])
			->add('pickupCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('cities')
						->where('cities.status = 1')
						->andWhere('cities.stateId = ' . $formData['pickupStateId'])
						->orderBy('cities.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData['pickupCityId'], 'status' => 1]),
			])
			->add('pickupZipCode', null, [
				'data' => $formData['pickupZipCode'],
			])
			->add('pickupEmail', null, [
				'data' => $formData['pickupEmail'],
			])
			->add('pickupContactNumber', null, [
				'data' => $formData['pickupContactNumber'],
			])
			->add('billingFirstName', null, [
				'data' => $formData['billingFirstName'],
			])
			->add('billingMidName', null, [
				'data' => $formData['billingMidName'],
			])
			->add('billingLastName', null, [
				'data' => $formData['billingLastName'],
			])
			->add('billingAddressLine1', null, [
				'data' => $formData['billingAddressLine1'],
			])
			->add('billingAddressLine2', null, [
				'data' => $formData['billingAddressLine2'],
			])
			->add('billingCountryId', EntityType::class, [
				'placeholder' => '-Select A Country-',
				'class' => 'NetFlexLocationBundle:Country',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('country')
						->where('country.status = 1')
						->orderBy('country.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => $formData['billingCountryId'], 'status' => 1]),
			])
			->add('billingStateId', EntityType::class, [
				'placeholder' => '-Select A State-',
				'class' => 'NetFlexLocationBundle:State',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('states')
						->where('states.status = 1')
						->andWhere('states.countryId = ' . $formData['billingCountryId'])
						->orderBy('states.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['billingStateId'], 'status' => 1]),
			])
			->add('billingCityId', EntityType::class, [
				'placeholder' => '-Select A City-',
				'class' => 'NetFlexLocationBundle:City',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('cities')
						->where('cities.status = 1')
						->andWhere('cities.stateId = ' . $formData['billingStateId'])
						->orderBy('cities.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData['billingCityId'], 'status' => 1]),
			])
			->add('billingZipCode', null, [
				'data' => (($formData) && ($formData['billingZipCode'])) ? $formData['billingZipCode'] : '',
			])
			->add('billingEmail', null, [
				'data' => $formData['billingEmail'],
			])
			->add('billingContactNumber', null, [
				'data' => $formData['billingContactNumber'],
			]);
		}
	}
}
