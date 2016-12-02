<?php

namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;
use NetFlex\UserBundle\Form\Front\ClientProfile\PreferredEmail;
use NetFlex\UserBundle\Form\Front\ClientProfile\PreferredContact;

class GeneralDetails extends UserType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('firstName')
				->add('midName')
				->add('lastName')
				->add('username')
				->add('emails', CollectionType::class, [
					'entry_type' => PreferredEmail::class,
				])
				->add('contacts', CollectionType::class, [
					'entry_type' => PreferredContact::class,
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
		return 'netflex_front_end_client_profile_general_details';
	}
}
