<?php

namespace NetFlex\OrderBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderItemForClientFromDashboardFormEventSubscriber implements EventSubscriberInterface
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
			$form->add('itemBaseWeight', null, [
				'data' => ($formData->getOrderId()->getOrderItem()->getItemBaseWeight() + $formData->getOrderId()->getOrderItem()->getItemAccountableExtraWeight()),
			])
			->add('itemWeightUnitId', EntityType::class, [
				'class' => 'NetFlexDeliveryChargeBundle:WeightUnit',
				'placeholder' => '-Select A Weight Unit-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('WU')
						->where('WU.status = 1')
						->orderBy('WU.id', 'ASC');
				},
			])
			->add('itemPrimaryTypeId', EntityType::class, [
				'class' => 'NetFlexOrderBundle:ItemType',
				'placeholder' => '-Select A Primary Type-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('IT')
						->where('IT.parentId is null')
						->andWhere('IT.status = 1')
						->orderBy('IT.itemTypeName', 'ASC');
				},
			])
			->add('itemSecondaryTypeId', EntityType::class, [
				'class' => 'NetFlexOrderBundle:ItemType',
				'placeholder' => '-Select A Secondary Type-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getItemPrimaryTypeId())) {
						return $er->createQueryBuilder('IT')
							->where('IT.parentId = ' . $formData->getItemPrimaryTypeId()->getId())
							->andWhere('IT.status = 1')
							->orderBy('IT.itemTypeName', 'ASC');
					} else {
						return $er->createQueryBuilder('ET')
							->where('IT.parentId = 1')
							->andWhere('IT.status = 1');
					}
				},
			])
			->add('itemCalculatedBaseWeight', HiddenType::class, [
				'mapped' => false,
			])
			->add('itemCalculatedWeightUnit', HiddenType::class, [
				'mapped' => false,
			]);
		} else {
			$form->add('itemWeightUnitId', EntityType::class, [
				'class' => 'NetFlexDeliveryChargeBundle:WeightUnit',
				'placeholder' => '-Select A Weight Unit-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					return $er->createQueryBuilder('WU')
						->where('WU.status = 1')
						->orderBy('WU.id', 'ASC');
				},
				'data' => (($formData) && ($formData->getItemWeightUnitId())) ?: $this->em->getReference('NetFlexDeliveryChargeBundle:WeightUnit', ['id' => 1, 'status' => 1])
			])
			->add('itemPrimaryTypeId', EntityType::class, [
				'class' => 'NetFlexOrderBundle:ItemType',
				'placeholder' => '-Select A Primary Type-',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('IT')
						->where('IT.parentId is null')
						->andWhere('IT.status = 1')
						->orderBy('IT.itemTypeName', 'ASC');
				},
				'data' => (($formData) && ($formData->getItemPrimaryTypeId())) ?: $this->em->getReference('NetFlexOrderBundle:ItemType', ['id' => 1, 'status' => 1])
			])
			->add('itemSecondaryTypeId', EntityType::class, [
				'class' => 'NetFlexOrderBundle:ItemType',
				'placeholder' => '-Select A Secondary Type-',
				'query_builder' => function(EntityRepository $er) use($formData) {
					if (($formData) && ($formData->getItemPrimaryTypeId())) {
						return $er->createQueryBuilder('IT')
							->where('IT.parentId = ' . $formData->getItemPrimaryTypeId()->getId())
							->andWhere('IT.status = 1')
							->orderBy('IT.itemTypeName', 'ASC');
					} else {
						return $er->createQueryBuilder('IT')
							->where('IT.parentId = 1')
							->andWhere('IT.status = 1');
					}
					
				},
				'data' => (($formData) && ($formData->getItemSecondaryTypeId())) ?: $this->em->getReference('NetFlexOrderBundle:ItemType', ['id' => 5, 'status' => 1])
			])
			->add('itemCalculatedBaseWeight', HiddenType::class, [
				'mapped' => false,
			])
			->add('itemCalculatedWeightUnit', HiddenType::class, [
				'mapped' => false,
			]);
		}
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
		
		$form->add('itemSecondaryTypeId', EntityType::class, [
			'class' => 'NetFlexOrderBundle:ItemType',
			'placeholder' => '-Select A Secondary Type-',
			'query_builder' => function(EntityRepository $er) use($formData) {
				if (($formData) && ($formData['itemPrimaryTypeId'])) {
					return $er->createQueryBuilder('ET')
						->where('ET.parentId = ' . $formData['itemPrimaryTypeId'])
						->andWhere('ET.status = 1')
						->orderBy('ET.itemTypeName', 'ASC');
				} else {
					return $er->createQueryBuilder('ET')
						->where('ET.parentId = 1')
						->andWhere('ET.status = 1');
				}
				
			},
			'data' => (($formData) && ($formData['itemSecondaryTypeId'])) ? $this->em->getReference('NetFlexOrderBundle:ItemType', ['id' => $formData['itemSecondaryTypeId'], 'status' => 1]) : $this->em->getReference('NetFlexOrderBundle:ItemType', ['id' => 5, 'status' => 1])
		]);
	}
}
