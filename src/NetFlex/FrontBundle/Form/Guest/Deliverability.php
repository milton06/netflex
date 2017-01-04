<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\FrontBundle\Form\EventSubscriber\Guest\DeliverabilityEventSubscriber;

class Deliverability extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('deliveryModeId', null, [
			'placeholder' => false,
			'expanded' => true,
			'multiple' => false,
		])
		->add('sourceCountryId', null, [
			'placeholder' => '-Select A Country-',
			'choices' => $options['countries'],
			'data' => $options['defaultCountry'],
		])
		->add('sourceStateId', null, [
			'placeholder' => '-Select A State-',
			'choices' => $options['states'],
			'data' => $options['defaultState'],
		])
		->add('sourceCityId', null, [
			'placeholder' => '-Select A City-',
			'choices' => $options['cities'],
			'data' => $options['defaultCity'],
		])
		->add('sourceZipCode')
		->add('destinationCountryId', null, [
			'placeholder' => '-Select A Country-',
			'choices' => $options['countries'],
			'data' => $options['defaultCountry'],
		])
		->add('destinationStateId', null, [
			'placeholder' => '-Select A State-',
			'choices' => $options['states'],
			'data' => $options['defaultState'],
		])
		->add('destinationCityId', null, [
			'placeholder' => '-Select A City-',
			'choices' => $options['cities'],
			'data' => $options['defaultCity'],
		])
		->add('destinationZipCode');
		
		$builder->addEventSubscriber(new DeliverabilityEventSubscriber());
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => DeliveryModeTimeline::class,
			'countries' => [],
			'defaultCountry' => null,
			'states' => [],
			'defaultState' => null,
			'cities' => [],
			'defaultCity' => null,
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'guest_check_shipment_deliverability';
	}
}
