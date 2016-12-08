<?php

namespace NetFlex\DeliveryChargeBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckDeliverabilityFormEventSubscriber implements EventSubscriberInterface
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
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}
	
	public function preSetData(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		if ('edit_order' === $this->request->get('_route')) {
			$form->add('sourceCountryId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:Country',
				'placeholder' => '-Select A Country-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('co')
						->where('co.status = 1')
						->orderBy('co.name', 'ASC');
				},
				'data' => $formData->getSourceCountryId(),
			]);
			
			$form->add('sourceStateId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:State',
				'placeholder' => '-Select A State-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('s')
						->where('s.countryId = ' . $formData->getSourceCountryId()->getId())
						->andWhere('s.status = 1')
						->orderBy('s.name', 'ASC');
				},
				'data' => $formData->getSourceStateId(),
			]);
			
			$form->add('sourceCityId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:City',
				'placeholder' => '-Select A City-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('ci')
						->where('ci.stateId = ' . $formData->getSourceStateId()->getId())
						->andWhere('ci.status = 1')
						->orderBy('ci.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData->getSourceCityId()->getId()]),
			]);
			
			$form->add('sourceZipCode', null, [
				'data' => $formData->getSourceZipCode(),
			]);
			
			$form->add('destinationCountryId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:Country',
				'placeholder' => '-Select A Country-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('co')
						->where('co.status = 1')
						->orderBy('co.name', 'ASC');
				},
				'data' => $formData->getDestinationCountryId(),
			]);
			
			$form->add('destinationStateId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:State',
				'placeholder' => '-Select A State-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('s')
						->where('s.countryId = ' . $formData->getDestinationCountryId()->getId())
						->andWhere('s.status = 1')
						->orderBy('s.name', 'ASC');
				},
				'data' => $formData->getDestinationStateId(),
			]);
			
			$form->add('destinationCityId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:City',
				'placeholder' => '-Select A City-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('ci')
						->where('ci.stateId = ' . $formData->getDestinationStateId()->getId())
						->andWhere('ci.status = 1')
						->orderBy('ci.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData->getDestinationCityId()->getId()]),
			]);
			
			$form->add('destinationZipCode', null, [
				'data' => $formData->getDestinationZipCode(),
			]);
		} else {
			$form->add('sourceCountryId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:Country',
				'placeholder' => '-Select A Country-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('co')
						->where('co.status = 1')
						->orderBy('co.name', 'ASC');
				},
				'data' => (($formData) && ($formData->getSourceCountryId())) ? $formData->getSourceCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1])
			])
			->add('sourceStateId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:State',
				'placeholder' => '-Select A State-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getSourceCountryId())) {
						return $er->createQueryBuilder('s')
							->where('s.countryId = ' . $formData->getSourceCountryId()->getId())
							->andWhere('s.status = 1')
							->orderBy('s.name', 'ASC');
					} else {
						return $er->createQueryBuilder('s')
							->where('s.countryId = 1')
							->andWhere('s.status = 1')
							->orderBy('s.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getSourceStateId())) ? $formData->getSourceStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1])
			])
			->add('sourceCityId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:City',
				'placeholder' => '-Select A City-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getSourceStateId())) {
						return $er->createQueryBuilder('ci')
							->where('ci.stateId = ' . $formData->getSourceStateId()->getId())
							->andWhere('ci.status = 1')
							->orderBy('ci.name', 'ASC');
					} else {
						return $er->createQueryBuilder('ci')
							->where('ci.stateId = 41')
							->andWhere('ci.status = 1')
							->orderBy('ci.name', 'ASC');
					}
				},
				'data' => (($formData) && ($formData->getSourceCityId())) ? $formData->getSourceCityId() : $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1])
			])
			->add('sourceZipCode')
			->add('destinationCountryId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:Country',
				'placeholder' => '-Select A Country-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('co')
						->where('co.status = 1')
						->orderBy('co.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', [
					'id' => 1,
					'status' => 1,
				]),
			])
			->add('destinationStateId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:State',
				'placeholder' => '-Select A State-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('s')
						->where('s.countryId = 1')
						->andWhere('s.status = 1')
						->orderBy('s.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', [
					'id' => 41,
					'status' => 1,
				])
			])
			->add('destinationCityId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:City',
				'placeholder' => '-Select A City-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('ci')
						->where('ci.stateId = 41')
						->andWhere('ci.status = 1')
						->orderBy('ci.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', [
					'id' => 5583,
					'status' => 1,
				])
			])
			->add('destinationZipCode');
		}
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		if ('edit_order' === $this->request->get('_route')) {
			//
		} else {
			$form->add('sourceCountryId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:Country',
				'placeholder' => '-Select A Country-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('co')
						->where('co.status = 1')
						->orderBy('co.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => $formData['sourceCountryId']])
			])
			->add('sourceStateId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:State',
				'placeholder' => '-Select A State-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('s')
						->where('s.countryId = 1')
						->andWhere('s.status = 1')
						->orderBy('s.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['sourceStateId']])
			])
			->add('sourceCityId', EntityType::class, [
				'class' => 'NetFlexLocationBundle:City',
				'placeholder' => '-Select A City-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('ci')
						->where('ci.stateId = 41')
						->andWhere('ci.status = 1')
						->orderBy('ci.name', 'ASC');
				},
				'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => $formData['sourceCityId']])
			])
			->add('sourceZipCode', null, [
				'data' => $formData['sourceZipCode']
			]);
		}
	}
}
