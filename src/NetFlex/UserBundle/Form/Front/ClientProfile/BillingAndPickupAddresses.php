<?php
namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;
use NetFlex\UserBundle\Form\Front\ClientProfile\BillingOrPickupAddress;

class BillingAndPickupAddresses extends UserType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('addresses', CollectionType::class, [
			'entry_type' => BillingOrPickupAddress::class,
			'allow_add' => true,
			'allow_delete' => true,
			'delete_empty' => true,
			'by_reference' => false,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
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
