<?php
namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;

class ChangePassword extends UserType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('password', PasswordType::class)
		->add('repeatPassword', PasswordType::class, [
			'mapped' => false,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'allow_extra_fields' => true,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'netflex_front_end_client_profile_change_password';
	}
}
