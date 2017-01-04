<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\FrontBundle\Form\Guest\OrderItem;
use NetFlex\FrontBundle\Form\Guest\OrderPrice;
use NetFlex\FrontBundle\Form\Guest\OrderAddresses;
use NetFlex\FrontBundle\Form\DataTransformer\Guest\NumberToDeliveryChargeDataTransformer;

class Order extends AbstractType
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('deliveryChargeId', HiddenType::class)
			->add('orderItem', OrderItem::class, [
				'primaryItemtypes' => $options['primaryItemtypes'],
				'defaultPrimaryType' => $options['defaultPrimaryType'],
				'secondaryItemtypes' => $options['secondaryItemtypes'],
				'defaultSecondaryItemtype' => $options['defaultSecondaryItemtype'],
				'weightUnits' => $options['weightUnits'],
				'defaultWeightUnit' => $options['defaultWeightUnit'],
			])
			->add('orderPrice', OrderPrice::class, [
				'currencyUnits' => $options['currencyUnits'],
				'defaultCurrencyUnit' => $options['defaultCurrencyUnit'],
				'riskTypes' => $options['riskTypes'],
			])
			->add('orderAddress', OrderAddresses::class, [
				'countries' => $options['countries'],
				'defaultCountry' => $options['defaultCountry'],
				'states' => $options['states'],
				'defaultState' => $options['defaultState'],
				'cities' => $options['cities'],
				'defaultCity' => $options['defaultCity'],
			]);
		
		$builder->get('deliveryChargeId')->addModelTransformer(new NumberToDeliveryChargeDataTransformer($this->em));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => OrderTransaction::class,
			'primaryItemtypes' => [],
			'defaultPrimaryType' => null,
			'secondaryItemtypes' => [],
			'defaultSecondaryItemtype' => null,
			'weightUnits' => [],
			'defaultWeightUnit' => null,
			'currencyUnits' => [],
			'defaultCurrencyUnit' => null,
			'riskTypes' => [],
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
		return 'guest_shipment_booking';
	}
}
