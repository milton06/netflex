<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NetFlex\OrderBundle\Entity\Item;
use NetFlex\FrontBundle\Form\EventSubscriber\Guest\OrderItemEventSubscriber;

class OrderItem extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('itemPrimaryTypeId', null, [
				'placeholder' => '-Select A Primary Type-',
				'choices' => $options['primaryItemtypes'],
				'data' => $options['defaultPrimaryType'],
			])
			->add('itemSecondaryTypeId', null, [
				'placeholder' => '-Select A Secondary Type-',
				'choices' => $options['secondaryItemtypes'],
				'data' => $options['defaultSecondaryItemtype'],
			])
			->add('itemBaseWeight')
			->add('itemWeightUnitId', null, [
				'placeholder' => '-Select A Weight Unit-',
				'choices' => $options['weightUnits'],
				'data' => $options['defaultWeightUnit'],
			])
			->add('itemCalculatedBaseWeight', HiddenType::class, [
				'mapped' => false,
			])
			->add('itemCalculatedWeightUnit', HiddenType::class, [
				'mapped' => false,
			])
			->add('itemAccountableExtraWeight', HiddenType::class, [
				'mapped' => false,
			])
			->add('itemAccountableExtraWeight', HiddenType::class);
		
		$builder->addEventSubscriber(new OrderItemEventSubscriber());
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Item::class,
			'primaryItemtypes' => [],
			'defaultPrimaryType' => null,
			'secondaryItemtypes' => [],
			'defaultSecondaryItemtype' => null,
			'weightUnits' => [],
			'defaultWeightUnit' => null,
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'guest_shipment_booking_item';
	}
}
