<?php
namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\Address;
use NetFlex\UserBundle\Form\AddressType;
use NetFlex\UserBundle\Form\EventSubscriber\Front\ClientProfile\BillingOrPickupAddressFormEventSubscriber;
use NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile\StringToAddressTypeTransformer;
use NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile\StringToCountryTransformer;
use NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile\StringToStateTransformer;
use NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile\StringToCityTransformer;

class BillingOrPickupAddress extends AddressType
{
	private $em;
	
	public function __construct(RequestStack $requestStack, EntityManager $em)
	{
		parent::__construct($requestStack, $em);
		$this->em = $em;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('addressTypeId', HiddenType::class)
				->add('addressLine1')
				->add('addressLine2')
				->add('countryId', TextType::class)
				->add('stateId', TextType::class)
				->add('cityId', TextType::class)
				->add('zipCode')
				->add('isPrimary');
		
		$builder->get('addressTypeId')->addModelTransformer(new StringToAddressTypeTransformer($this->em));
		$builder->get('countryId')->addModelTransformer(new StringToCountryTransformer($this->em));
		$builder->get('stateId')->addModelTransformer(new StringToStateTransformer($this->em));
		$builder->get('cityId')->addModelTransformer(new StringToCityTransformer($this->em));
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Address::class,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'netflex_front_end_client_profile_addresses';
	}
}
