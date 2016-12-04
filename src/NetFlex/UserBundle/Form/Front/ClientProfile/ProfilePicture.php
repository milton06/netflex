<?php
namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;

class ProfilePicture extends UserType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('profileImage', FileType::class);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'validation_groups' => ['update_profile_image'],
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'netflex_front_end_client_profile_profile_picture';
	}
}
