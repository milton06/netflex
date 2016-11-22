<?php

namespace NetFlex\UserBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressFormEditEventSubscriber implements EventSubscriberInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::POST_SET_DATA => 'postSetData',
		];
	}
	
	public function postSetData(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		$form->add('countryId', EntityType::class, [
			'placeholder' => '-Select A Country-',
			'class' => 'NetFlexLocationBundle:Country',
			'query_builder' => function(EntityRepository $er) {
				return $er->createQueryBuilder('country')
					->where('country.status = 1')
					->orderBy('country.name', 'ASC');
			},
			'data' => (null !== $formData) ? $formData->getCountryId() : $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1])
		]);
		$form->add('stateId', EntityType::class, [
			'placeholder' => '-Select A State-',
			'class' => 'NetFlexLocationBundle:State',
			'query_builder' => function(EntityRepository $er) {
				return $er->createQueryBuilder('states')
					->where('states.status = 1')
					->orderBy('states.name', 'ASC');
			},
			'data' => (null !== $formData) ? $formData->getStateId() : $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
		]);
		$form->add('cityId', EntityType::class, [
			'placeholder' => '-Select A City-',
			'class' => 'NetFlexLocationBundle:City',
			'query_builder' => function(EntityRepository $er) {
				return $er->createQueryBuilder('cities')
					->where('cities.status = 1')
					->orderBy('cities.name', 'ASC');
			},
			'data' => (null !== $formData) ? $formData->getCityId() : $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1])
		]);
		$form->add('addressTypeId', EntityType::class, [
			'placeholder' => '-Select An Address Type-',
			'class' => 'NetFlexUserBundle:AddressType',
			'query_builder' => function(EntityRepository $er) {
				return $er->createQueryBuilder('addressType')
					->where('addressType.id = 1 OR addressType.id = 2')
					->andWhere('addressType.status = 1')
					->orderBy('addressType.id', 'ASC');
			},
			'data' => (null !== $formData) ? $formData->getAddressTypeId() : $this->em->getReference('NetFlexUserBundle:AddressType', ['id' => 1, 'status' => 1]),
		]);
	}
}
