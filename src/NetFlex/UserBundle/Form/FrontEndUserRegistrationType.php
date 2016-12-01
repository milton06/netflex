<?php

namespace NetFlex\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;
use NetFlex\UserBundle\Form\FrontEndUserRegistrationEmailType;
use NetFlex\UserBundle\Form\FrontEndUserRegistrationContactType;

class FrontEndUserRegistrationType extends UserType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username')
				->add('password', PasswordType::class)
				->add('firstName')
				->add('lastName')
				->add('emails', CollectionType::class, [
					'entry_type' => FrontEndUserRegistrationEmailType::class,
				])
				->add('contacts', CollectionType::class, [
					'entry_type' => FrontEndUserRegistrationContactType::class,
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
		return 'netflex_front_end_user_registration';
	}
}
