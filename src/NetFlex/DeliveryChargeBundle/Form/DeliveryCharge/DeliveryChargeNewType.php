<?php

namespace NetFlex\DeliveryChargeBundle\Form\DeliveryCharge;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryChargeNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sourceCountryId', EntityType::class, [
            'placeholder' => '-Select A Source Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'choices' => $options['sourceCountries'],
            'data' => $options['defaultSourceCountry'],
        ])
        ->add('sourceStateId', EntityType::class, [
            'placeholder' => '-Select A Source State-',
            'class' => 'NetFlexLocationBundle:State',
            'choices' => $options['sourceStates'],
            'data' => $options['defaultSourceState'],
        ])
        ->add('sourceCityId', EntityType::class, [
            'placeholder' => '-Select A Source City-',
            'class' => 'NetFlexLocationBundle:City',
            'choices' => $options['sourceCities'],
            'data' => $options['defaultSourceCity'],
        ])
        ->add('destinationCountryId', EntityType::class, [
            'placeholder' => '-Select A Destination Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'choices' => $options['destinationCountries'],
            'data' => $options['defaultDestinationCountry'],
        ])
        ->add('destinationStateId', EntityType::class, [
            'placeholder' => '-Select A Destination State-',
            'class' => 'NetFlexLocationBundle:State',
            'choices' => $options['destinationStates'],
            'data' => $options['defaultDestinationState'],
        ])
        ->add('destinationCityId', EntityType::class, [
            'placeholder' => '-Select A Destination City-',
            'class' => 'NetFlexLocationBundle:City',
            'choices' => $options['destinationCities'],
            'data' => $options['defaultDestinationCity'],
        ])
        ->add('deliveryModeId', EntityType::class, [
            'placeholder' => false,
            'class' => 'NetFlexDeliveryChargeBundle:DeliveryMode',
            'choices' => $options['deliveryModes'],
            'expanded' => true,
        ])
        ->add('deliveryTimelineId', EntityType::class, [
            'placeholder' => '-Select A Delivery Timeline-',
            'class' => 'NetFlexDeliveryChargeBundle:DeliveryTimeline',
            'choices' => $options['deliveryTimelines'],
        ])
        ->add('baseWeight', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('extraWeight', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('weightUnitId', EntityType::class, [
            'placeholder' => '-Select A Weight Unit',
            'class' => 'NetFlexDeliveryChargeBundle:WeightUnit',
            'choices' => $options['weightUnits'],
            'data' => $options['defaultWeightUnit']
        ])
        ->add('basePrice', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('extraPriceMultiplier', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('codBasePrice', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('fuelSurchargePercentageOnBasePrice', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('serviceTaxPercentageOnBasePrice', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('carrierRiskPercentageOnBasePrice', NumberType::class, [
            'scale' => 2,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_CEILING,
        ])
        ->add('currencyUnitId', EntityType::class, [
            'placeholder' => '-Select A Currency Unit',
            'class' => 'NetFlexDeliveryChargeBundle:Currency',
            'choices' => $options['currencyUnits'],
            'data' => $options['defaultCurrencyUnit']
        ]);
        
        if (1 == $options['deliveryZone']) {
            $builder->add('sourceZipCodeRange', TextType::class)
            ->add('destinationZipCodeRange', TextType::class);
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'deliveryZone' => null,
            'sourceCountries' => [],
            'defaultSourceCountry' => null,
            'sourceStates' => [],
            'defaultSourceState' => null,
            'sourceCities' => [],
            'defaultSourceCity' => null,
            'destinationCountries' => [],
            'defaultDestinationCountry' => null,
            'destinationStates' => [],
            'defaultDestinationState' => null,
            'destinationCities' => [],
            'defaultDestinationCity' => null,
            'deliveryModes' => [],
            'deliveryTimelines' => [],
            'weightUnits' => [],
            'defaultWeightUnit' => null,
            'currencyUnits' => [],
            'defaultCurrencyUnit' => null,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'delivery_charge_new';
    }
}
