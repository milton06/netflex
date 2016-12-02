<?php
namespace NetFlex\UserBundle\Form\Front\ClientProfile;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\UserBundle\Entity\Email;
use NetFlex\UserBundle\Form\EmailType;

class PreferredEmail extends EmailType
{
	public function __construct(RequestStack $requestStack)
	{
		parent::__construct($requestStack);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Email::class,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'netflex_front_end_client_profile_preferred_email';
	}
}
