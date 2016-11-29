<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\OrderBundle\Entity\Address;
use NetFlex\OrderBundle\Form\AddressType;
use NetFlex\OrderBundle\Form\EventSubscriber\OrderAddressForClientFromDashboardFormEventSubscriber;

class OrderAddressForClientFromDashboardType extends AddressType
{
	private $em;
	private $request;
	
	public function __construct(EntityManager $em, RequestStack $requestStack)
	{
		$this->em = $em;
		$this->request = $requestStack->getCurrentRequest();
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('pickupFirstName')
		    ->add('pickupMidName')
		    ->add('pickupLastName')
		    ->add('pickupAddressLine1')
		    ->add('pickupAddressLine2')
		    ->add('pickupCountryId')
		    ->add('pickupStateId')
		    ->add('pickupCityId')
		    ->add('pickupZipCode')
		    ->add('pickupLandMark')
		    ->add('pickupEmail')
		    ->add('pickupContactNumber')
		    ->add('billingFirstName')
		    ->add('billingMidName')
		    ->add('billingLastName')
		    ->add('billingAddressLine1')
		    ->add('billingAddressLine2')
		    ->add('billingCountryId')
		    ->add('billingStateId')
		    ->add('billingCityId')
		    ->add('billingZipCode')
		    ->add('billingLandMark')
		    ->add('billingEmail')
		    ->add('billingContactNumber')
		    ->add('shippingFirstName')
		    ->add('shippingMidName')
		    ->add('shippingLastName')
		    ->add('shippingAddressLine1')
		    ->add('shippingAddressLine2')
		    ->add('shippingCountryId')
		    ->add('shippingStateId')
		    ->add('shippingCityId')
		    ->add('shippingZipCode')
		    ->add('shippingLandMark')
		    ->add('shippingEmail')
		    ->add('shippingContactNumber');
	    
	    $builder->addEventSubscriber(new OrderAddressForClientFromDashboardFormEventSubscriber($this->em, $this->request, $options['clientOtherPickupAddresses'], $options['clientOtherBillingAddresses']));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
	        'clientOtherPickupAddresses' => [],
	        'clientOtherBillingAddresses' => [],
        ));
    }
}
