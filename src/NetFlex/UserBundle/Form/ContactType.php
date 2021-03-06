<?php

namespace NetFlex\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use NetFlex\UserBundle\Entity\Contact;

class ContactType extends AbstractType
{
	private $request;
	
	public function __construct(RequestStack $requestStack)
	{
		$this->request = $requestStack->getCurrentRequest();
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contactNumber')
	            ->add('isPrimary');
	
	    if ('edit_client_profile_from_dashboard' === $this->request->get('_route')) {
		    $builder->add('status');
	    }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => Contact::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'netflex_userbundle_contact';
    }
}
