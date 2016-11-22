<?php

namespace NetFlex\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
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
	private $em;
	
	public function __construct(RequestStack $requestStack, EntityManager $em)
	{
		$this->request = $requestStack->getCurrentRequest();
		$this->em = $em;
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    if ('register_client_from_dashboard' === $this->request->get('_route')) {
		    $builder->add('addressLine1')
			    ->add('addressLine2', null, [
			    	'required' => false,
			    ])
			    ->add('countryId', EntityType::class, [
				    'placeholder' => '-Select A Country-',
				    'class' => 'NetFlexLocationBundle:Country',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('country')
						    ->where('country.status = 1')
						    ->orderBy('country.name', 'ASC');
				    },
				    'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
			    ])
			    ->add('stateId', EntityType::class, [
				    'placeholder' => '-Select A State-',
				    'class' => 'NetFlexLocationBundle:State',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('states')
						    ->where('states.status = 1')
						    ->orderBy('states.name', 'ASC');
				    },
				    'data' => $this->em->getReference('NetFlexLocationBundle:State', ['id' => 41, 'status' => 1]),
			    ])
			    ->add('cityId', EntityType::class, [
				    'placeholder' => '-Select A City-',
				    'class' => 'NetFlexLocationBundle:City',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('cities')
						    ->where('cities.status = 1')
						    ->orderBy('cities.name', 'ASC');
				    },
				    'data' => $this->em->getReference('NetFlexLocationBundle:City', ['id' => 5583, 'status' => 1])
			    ])
			    ->add('addressTypeId', EntityType::class, [
				    'placeholder' => '-Select An Address Type-',
				    'class' => 'NetFlexUserBundle:AddressType',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('addressType')
						    ->where('addressType.id = 1 OR addressType.id = 2')
						    ->andWhere('addressType.status = 1')
						    ->orderBy('addressType.id', 'ASC');
				    },
				    'data' => $this->em->getReference('NetFlexUserBundle:AddressType', ['id' => 1, 'status' => 1]),
			    ])
			    ->add('zipCode')
			    ->add('isPrimary');
	    } elseif ('edit_client_profile_from_dashboard' === $this->request->get('_route')) {
		    $builder->add('addressLine1')
			    ->add('addressLine2', null, [
				    'required' => false,
			    ])
			    ->add('countryId', EntityType::class, [
				    'placeholder' => '-Select A Country-',
				    'class' => 'NetFlexLocationBundle:Country',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('country')
						    ->where('country.status = 1')
						    ->orderBy('country.name', 'ASC');
				    }
			    ])
			    ->add('stateId', EntityType::class, [
				    'placeholder' => '-Select A State-',
				    'class' => 'NetFlexLocationBundle:State',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('states')
						    ->where('states.status = 1')
						    ->orderBy('states.name', 'ASC');
				    }
			    ])
			    ->add('cityId', EntityType::class, [
				    'placeholder' => '-Select A City-',
				    'class' => 'NetFlexLocationBundle:City',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('cities')
						    ->where('cities.status = 1')
						    ->orderBy('cities.name', 'ASC');
				    }
			    ])
			    ->add('addressTypeId', EntityType::class, [
				    'placeholder' => '-Select An Address Type-',
				    'class' => 'NetFlexUserBundle:AddressType',
				    'query_builder' => function(EntityRepository $er) {
					    return $er->createQueryBuilder('addressType')
						    ->where('addressType.id = 1 OR addressType.id = 2')
						    ->andWhere('addressType.status = 1')
						    ->orderBy('addressType.id', 'ASC');
				    }
			    ])
			    ->add('zipCode')
			    ->add('isPrimary');
	    }
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
