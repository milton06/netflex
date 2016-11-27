<?php

namespace NetFlex\OrderBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderPriceForClientFromDashboardFormEventSubscriber implements EventSubscriberInterface
{
	private $em;
	private $orderRiskTypes;
	
	public function __construct(EntityManager $em, $orderRiskTypes)
	{
		$this->em = $em;
		$this->orderRiskTypes = $orderRiskTypes;
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
		
		$form->add('orderPriceUnitId', EntityType::class, [
			'class' => 'NetFlexDeliveryChargeBundle:Currency',
			'placeholder' => '-Select A Currency Unit-',
			'query_builder' => function(EntityRepository $er) use($formData) {
				return $er->createQueryBuilder('C')
					->where('C.status = 1')
					->orderBy('C.id', 'ASC');
			},
			'data' => (($formData) && ($formData->getOrderPriceUnitId())) ?: $this->em->getReference('NetFlexDeliveryChargeBundle:Currency', ['id' => 1, 'status' => 1])
		])
		->add('codCoice', ChoiceType::class, [
			'placeholder' => false,
			'choices' => [
				'No' => '0',
				'Yes' => '1',
			],
			'expanded' => true,
			'multiple' => false,
			'mapped' => false,
		])
		->add('riskType', ChoiceType::class, [
			'placeholder' => false,
			'choices' => $this->orderRiskTypes,
			'expanded' => true,
			'multiple' => false,
			'mapped' => false,
		]);
	}
}
