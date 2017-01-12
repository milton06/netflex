<?php

namespace NetFlex\DeliveryChargeBundle\Form\DeliveryCharge;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use NetFlex\DeliveryChargeBundle\Form\EventSubscriber\DeliveryCharge\DeliveryChargeNewTypeEventSubscriber;

class DeliveryChargeNewType extends AbstractType
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['actionUrl'])
        ->setMethod("POST")
        ->add('sourceCountryId', EntityType::class, [
            'placeholder' => '-Select A Source Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'choices' => $options['sourceCountries'],
            'data' => $options['defaultSourceCountry'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Source country is required',
                ]),
            ],
        ])
        ->add('sourceStateId', EntityType::class, [
            'placeholder' => '-Select A Source State-',
            'class' => 'NetFlexLocationBundle:State',
            'choices' => $options['sourceStates'],
            'data' => $options['defaultSourceState'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Source state is required',
                ]),
            ],
        ])
        ->add('sourceCityId', EntityType::class, [
            'placeholder' => '-Select A Source City-',
            'class' => 'NetFlexLocationBundle:City',
            'choices' => $options['sourceCities'],
            'data' => $options['defaultSourceCity'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Source city is required',
                ]),
            ],
        ])
        ->add('destinationCountryId', EntityType::class, [
            'placeholder' => '-Select A Destination Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'choices' => $options['destinationCountries'],
            'data' => $options['defaultDestinationCountry'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination country is required',
                ]),
            ],
        ])
        ->add('destinationStateId', EntityType::class, [
            'placeholder' => '-Select A Destination State-',
            'class' => 'NetFlexLocationBundle:State',
            'choices' => $options['destinationStates'],
            'data' => $options['defaultDestinationState'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination state is required',
                ]),
            ],
        ])
        ->add('destinationCityId', EntityType::class, [
            'placeholder' => '-Select A Destination City-',
            'class' => 'NetFlexLocationBundle:City',
            'choices' => $options['destinationCities'],
            'data' => $options['defaultDestinationCity'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Destination city is required',
                ]),
            ],
        ])
        ->add('deliveryModeId', EntityType::class, [
            'placeholder' => false,
            'class' => 'NetFlexDeliveryChargeBundle:DeliveryMode',
            'choices' => $options['deliveryModes'],
            'expanded' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Delivery mode is required',
                ]),
            ],
        ])
        ->add('deliveryTimelineId', EntityType::class, [
            'placeholder' => '-Select A Delivery Timeline-',
            'class' => 'NetFlexDeliveryChargeBundle:DeliveryTimeline',
            'choices' => $options['deliveryTimelines'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Delivery timeline is required',
                ]),
            ],
        ])
        ->add('baseWeight', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Base weight is required',
                ]),
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Base weight must be a number',
                ]),
            ],
        ])
        ->add('extraWeight', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Extra weight must be a number',
                ]),
            ],
        ])
        ->add('weightUnitId', EntityType::class, [
            'placeholder' => '-Select A Weight Unit',
            'class' => 'NetFlexDeliveryChargeBundle:WeightUnit',
            'choices' => $options['weightUnits'],
            'data' => $options['defaultWeightUnit'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Weight unit is required',
                ]),
            ],
        ])
        ->add('basePrice', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Base price is required',
                ]),
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Base price must be a number',
                ]),
            ],
        ])
        ->add('extraPriceMultiplier', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Extra price must be a number',
                ]),
            ],
        ])
        ->add('codBasePrice', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'COD charge must be a number',
                ]),
            ],
        ])
        ->add('fuelSurchargePercentageOnBasePrice', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Base price must be a number',
                ]),
            ],
        ])
        ->add('serviceTaxPercentageOnBasePrice', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Service charge must be a number',
                ]),
            ],
        ])
        ->add('carrierRiskPercentageOnBasePrice', null, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[1-9]\d*(\.\d+)?$/',
                    'htmlPattern' => false,
                    'message' => 'Carrier risk charge must be a number',
                ]),
            ],
        ])
        ->add('currencyUnitId', EntityType::class, [
            'placeholder' => '-Select A Currency Unit',
            'class' => 'NetFlexDeliveryChargeBundle:Currency',
            'choices' => $options['currencyUnits'],
            'data' => $options['defaultCurrencyUnit'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Currency unit is required',
                ]),
            ],
        ]);
        
        if (1 == $options['deliveryZone']) {
            $builder->add('sourceZipCodeRange')
            ->add('destinationZipCodeRange');
        }
        
        $builder->addEventSubscriber(new DeliveryChargeNewTypeEventSubscriber($this->em));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'allow_extra_data' => true,
            'actionUrl' => null,
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
