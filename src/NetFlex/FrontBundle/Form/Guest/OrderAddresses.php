<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NetFlex\OrderBundle\Entity\Address;
use NetFlex\FrontBundle\Form\EventSubscriber\Guest\OrderAddressesEventSubscriber;

class OrderAddresses extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('pickupFirstName')
			->add('pickupMidName')
			->add('pickupLastName')
			->add('pickupAddressLine1')
			->add('pickupAddressLine2')
			->add('pickupCountryId', null, [
				'placeholder' => '-Select A Country-',
				'choices' => $options['countries'],
				'data' => $options['defaultCountry'],
			])
			->add('pickupStateId', null, [
				'placeholder' => '-Select A State-',
				'choices' => $options['states'],
				'data' => $options['defaultState'],
			])
			->add('pickupCityId', null, [
				'placeholder' => '-Select A City-',
				'choices' => $options['cities'],
				'data' => $options['defaultCity'],
			])
			->add('pickupZipCode')
			->add('pickupLandMark')
			->add('pickupEmail', EmailType::class)
			->add('pickupContactNumber')
			->add('billingFirstName')
			->add('billingMidName')
			->add('billingLastName')
			->add('billingAddressLine1')
			->add('billingAddressLine2')
			->add('billingCountryId', null, [
				'placeholder' => '-Select A Country-',
				'choices' => $options['countries'],
				'data' => $options['defaultCountry'],
			])
			->add('billingStateId', null, [
				'placeholder' => '-Select A State-',
				'choices' => $options['states'],
				'data' => $options['defaultState'],
			])
			->add('billingCityId', null, [
				'placeholder' => '-Select A City-',
				'choices' => $options['cities'],
				'data' => $options['defaultCity'],
			])
			->add('billingZipCode')
			->add('billingLandMark')
			->add('billingEmail', EmailType::class)
			->add('billingContactNumber')
			->add('shippingFirstName')
			->add('shippingMidName')
			->add('shippingLastName')
			->add('shippingAddressLine1')
			->add('shippingAddressLine2')
			->add('shippingCountryId', null, [
				'placeholder' => '-Select A Country-',
				'choices' => $options['countries'],
				'data' => $options['defaultCountry'],
			])
			->add('shippingStateId', null, [
				'placeholder' => '-Select A State-',
				'choices' => $options['states'],
				'data' => $options['defaultState'],
			])
			->add('shippingCityId', null, [
				'placeholder' => '-Select A City-',
				'choices' => $options['cities'],
				'data' => $options['defaultCity'],
			])
			->add('shippingZipCode')
			->add('shippingLandMark')
			->add('shippingEmail', EmailType::class)
			->add('shippingContactNumber');
		
		$builder->addEventSubscriber(new OrderAddressesEventSubscriber());
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Address::class,
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
		return 'guest_shipment_booking_address';
	}
}
