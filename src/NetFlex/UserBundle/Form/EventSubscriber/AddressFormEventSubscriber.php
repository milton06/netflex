<?php

namespace NetFlex\UserBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressFormEventSubscriber implements EventSubscriberInterface
{
	private $request;
	
	public function __construct(Request $request)
	{
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
		
		$form->add('addressTypeId', EntityType::class, [
			'placeholder' => '-Select An Address Type-',
			'class' => 'NetFlexUserBundle:AddressType',
			'query_builder' => function(EntityRepository $er) {
				if ('register_client_from_dashboard' === $this->request->get('_route')) {
					return $er->createQueryBuilder('AT')
						->where('AT.id = 1 OR AT.id = 2')
						->andWhere('AT.status = 1')
						->orderBy('AT.id', 'ASC');
				} else {
					return $er->createQueryBuilder('AT')
						->orderBy('AT.id', 'ASC');
				}
			}
		]);
	}
	
	public function preSubmit(FormEvent $formEvent)
	{
		$form = $formEvent->getForm();
		$formData = $formEvent->getData();
	}
}
