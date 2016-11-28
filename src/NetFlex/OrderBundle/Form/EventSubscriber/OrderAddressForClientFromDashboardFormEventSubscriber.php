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
	
	public function __construct(EntityManager $em, Request $request)
	{
		$this->em = $em;
		$this->request = $request;
	}
	
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
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
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupFirstName())) ? true : false,
				],
			])
			->add('pickupMidName', null, [
				'data' => (($formData) && ($formData->getPickupMidName())) ? $formData->getPickupMidName() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupMidName())) ? true : false,
				],
			])
			->add('pickupLastName', null, [
				'data' => (($formData) && ($formData->getPickupLastName())) ? $formData->getPickupLastName() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupLastName())) ? true : false,
				],
			])
			->add('pickupAddressLine1', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine1())) ? $formData->getPickupAddressLine1() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupAddressLine1())) ? true : false,
				],
			])
			->add('pickupAddressLine2', null, [
				'data' => (($formData) && ($formData->getPickupAddressLine2())) ? $formData->getPickupAddressLine2() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupAddressLine2())) ? true : false,
				],
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
					'disabled' => (($formData) && ($formData->getPickUpCountryId())) ? true : false,
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
					'disabled' => (($formData) && ($formData->getPickUpStateId())) ? true : false,
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
					'disabled' => (($formData) && ($formData->getPickUpCityId())) ? true : false,
				],
			])
			->add('pickupZipCode', null, [
				'data' => (($formData) && ($formData->getPickupZipCode())) ? $formData->getPickupZipCode() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getPickupZipCode())) ? true : false,
				],
			])
			->add('billingFirstName', null, [
				'data' => (($formData) && ($formData->getBillingFirstName())) ? $formData->getBillingFirstName() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingFirstName())) ? true : false,
				],
			])
			->add('billingMidName', null, [
				'data' => (($formData) && ($formData->getBillingMidName())) ? $formData->getBillingMidName() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingMidName())) ? true : false,
				],
			])
			->add('billingLastName', null, [
				'data' => (($formData) && ($formData->getBillingLastName())) ? $formData->getBillingLastName() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingLastName())) ? true : false,
				],
			])
			->add('billingAddressLine1', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine1())) ? $formData->getBillingAddressLine1() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingAddressLine1())) ? true : false,
				],
			])
			->add('billingAddressLine2', null, [
				'data' => (($formData) && ($formData->getBillingAddressLine2())) ? $formData->getBillingAddressLine2() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingAddressLine2())) ? true : false,
				],
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
					'disabled' => (($formData) && ($formData->getBillingCountryId())) ? true : false,
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
					'disabled' => (($formData) && ($formData->getBillingStateId())) ? true : false,
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
					'disabled' => (($formData) && ($formData->getBillingCityId())) ? true : false,
				],
			])
			->add('billingZipCode', null, [
				'data' => (($formData) && ($formData->getBillingZipCode())) ? $formData->getBillingZipCode() : '',
				'attr' => [
					'disabled' => (($formData) && ($formData->getBillingZipCode())) ? true : false,
				],
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
		}
	}
}
