<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\OrderBundle\Form\OrderTransactionType;
use NetFlex\OrderBundle\Form\OrderItemForClientFromDashboardType;
use NetFlex\OrderBundle\Form\DataTransformer\IdToUserTransformer;
use NetFlex\OrderBundle\Form\DataTransformer\IdToDeliveryChargeTransformer;
use NetFlex\OrderBundle\Form\EventSubscriber\OrderForClientFromDashboardFormEventSubscriber;

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
        ->add('deliveryChargeId', HiddenType::class);
	    
	    if ('edit_order' === $this->request->get('_route')) {
		    //
	    } else {
		    $builder->add('userId', HiddenType::class, [
			    'data' => $this->request->get('clientId'),
		    ]);
		
		    $builder->get('userId')->addModelTransformer(new IdToUserTransformer($this->request->get('clientId'), $this->em));
	    }
	    
	    $builder->addEventSubscriber(new OrderForClientFromDashboardFormEventSubscriber($this->em, $this->request));
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
