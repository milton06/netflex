<?php

namespace NetFlex\DeliveryChargeBundle\Form\DeliveryCharge;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\DeliveryChargeBundle\Form\EventSubscriber\DeliveryCharge\DeliveryChargeSearchTypeEventSubscriber;

class DeliveryChargeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['actionUrl'])
        ->setMethod("POST")
        ->add('sourceCountryId', ChoiceType::class, [
            'placeholder' => '-All Source Countries-',
            'choices' => $options['countryList'],
            'data' => $options['sourceCountryId'],
            'mapped' => false,
        ])
        ->add('sourceStateId', ChoiceType::class, [
            'placeholder' => '-All Source States-',
            'choices' => $options['sourceStateList'],
            'data' => $options['sourceStateId'],
            'mapped' => false,
        ])
        ->add('sourceCityId', ChoiceType::class, [
            'placeholder' => '-All Source Cities-',
            'choices' => $options['sourceCityList'],
            'data' => $options['sourceCityId'],
            'mapped' => false,
        ])
        ->add('destinationCountryId', ChoiceType::class, [
            'placeholder' => '-All Destination Countries-',
            'choices' => $options['countryList'],
            'data' => $options['destinationCountryId'],
            'mapped' => false,
        ])
        ->add('destinationStateId', ChoiceType::class, [
            'placeholder' => '-All Destination States-',
            'choices' => $options['destinationStateList'],
            'data' => $options['destinationStateId'],
            'mapped' => false,
        ])
        ->add('destinationCityId', ChoiceType::class, [
            'placeholder' => '-All Destination Cities-',
            'choices' => $options['destinationCityList'],
            'data' => $options['destinationCityId'],
            'mapped' => false,
        ]);
        
        $builder->addEventSubscriber(new DeliveryChargeSearchTypeEventSubscriber($options));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'actionUrl' => null,
            'countryList' => [],
            'sourceCountryId' => null,
            'sourceStateList' => [],
            'sourceStateId' => null,
            'sourceCityList' => [],
            'sourceCityId' => null,
            'destinationCountryId' => null,
            'destinationStateList' => [],
            'destinationStateId' => null,
            'destinationCityList' => [],
            'destinationCityId' => null,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'delivery_charge_search';
    }
}
