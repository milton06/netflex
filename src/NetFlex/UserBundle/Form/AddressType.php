<?php

namespace NetFlex\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use NetFlex\UserBundle\Form\EventSubscriber\AddressFormEventSubscriber;
use NetFlex\UserBundle\Entity\Address;

class AddressType extends AbstractType
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
	    $builder->add('addressLine1')
	            ->add('addressLine2')
		        ->add('countryId', null, [
		        	'placeholder' => '-Select A Country-',
		        ])
		        ->add('stateId', null, [
		        	'placeholder' => '-Select A State-'
		        ])
		        ->add('cityId', null, [
		        	'placeholder' => '-Select A City-',
		        ])
		        ->add('isPrimary');
	    
	    $builder->addEventSubscriber(new AddressFormEventSubscriber($this->request));
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
        return 'netflex_userbundle_address';
    }
}
