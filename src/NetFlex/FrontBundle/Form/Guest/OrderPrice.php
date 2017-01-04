<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NetFlex\OrderBundle\Entity\Price;

class OrderPrice extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('orderInvoicePrice')
			->add('orderPriceUnitId', null, [
				'placeholder' => '-Select A Currency Unit-',
				'choices' => $options['currencyUnits'],
				'data' => $options['defaultCurrencyUnit'],
			])
			->add('riskType', ChoiceType::class, [
				'expanded' => true,
				'choices' => $options['riskTypes'],
				'data' => $options['riskTypes']['Own Risk'],
				'mapped' => false,
			])
			->add('orderBaseCharge', HiddenType::class)
			->add('orderExtraWeightLeviedCharge', HiddenType::class)
			->add('orderFuelSurchargeAddedCharge', HiddenType::class)
			->add('orderServiceTaxAddedCharge', HiddenType::class)
			->add('orderCarrierRiskAddedCharge', HiddenType::class);
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Price::class,
			'currencyUnits' => [],
			'defaultCurrencyUnit' => null,
			'riskTypes' => [],
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'guest_shipment_booking_price';
	}
}
