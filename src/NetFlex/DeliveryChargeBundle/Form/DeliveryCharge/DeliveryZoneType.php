<?php

namespace NetFlex\DeliveryChargeBundle\Form\DeliveryCharge;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryZoneType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->setAction($options['actionUrl'])
		->setMethod('POST')
		->add('deliveryZone', ChoiceType::class, [
			'placeholder' => '-Select A Delivery Zone-',
			'choices' => $options['deliveryZones'],
			'data' => $options['defaultDeliveryZone'],
		]);
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
			'actionUrl' => null,
			'deliveryZones' => [],
			'defaultDeliveryZone' => null,
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'delivery_zone';
	}
}
