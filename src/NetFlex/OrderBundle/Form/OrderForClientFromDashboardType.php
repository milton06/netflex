<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
use NetFlex\OrderBundle\Form\OrderItemForClientFromDashboardType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\OrderBundle\Form\OrderTransactionType;
use NetFlex\OrderBundle\Form\DataTransformer\IdToUserTransformer;

class OrderForClientFromDashboardType extends OrderTransactionType
{
    private $request;
	private $em;
	
	public function __construct(RequestStack $requestStack, EntityManager $em) {
		$this->request = $requestStack->getCurrentRequest();
		$this->em = $em;
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderItem', OrderItemForClientFromDashboardType::class)
        ->add('orderPrice', OrderPriceForClientFromDashboardType::class)
        ->add('orderAddress', OrderAddressForClientFromDashboardType::class)
	    ->add('userId', HiddenType::class, [
	    	'data' => $this->request->get('clientId'),
	    ]);
	    
	    $builder->get('userId')->addModelTransformer(new IdToUserTransformer($this->request->get('clientId'), $this->em));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderTransaction::class,
        ));
    }
}
