<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\OrderBundle\Entity\Price;
use NetFlex\OrderBundle\Form\PriceType;
use NetFlex\OrderBundle\Form\EventSubscriber\OrderPriceForClientFromDashboardFormEventSubscriber;

class OrderPriceForClientFromDashboardType extends PriceType
{
	private $em;
	private $orderRiskTypes;
	private $request;
	
	public function __construct(EntityManager $em, $orderRiskTypes, RequestStack $requestStack)
	{
		$this->em = $em;
		$this->orderRiskTypes = $orderRiskTypes;
		$this->request = $requestStack->getCurrentRequest();
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderInvoicePrice')
        ->add('orderBaseCharge', HiddenType::class)
        ->add('orderExtraWeightLeviedCharge', HiddenType::class)
        ->add('orderCodPaymentAddedCharge', HiddenType::class)
        ->add('orderFuelSurchargeAddedCharge', HiddenType::class)
        ->add('orderServiceTaxAddedCharge', HiddenType::class)
        ->add('orderCarrierRiskAddedCharge', HiddenType::class)
        ->add('orderOctroiCharge', HiddenType::class)
        ->add('orderReturnCharge', HiddenType::class);
	
	    $builder->addEventSubscriber(new OrderPriceForClientFromDashboardFormEventSubscriber($this->em, $this->orderRiskTypes, $this->request));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Price::class,
        ));
    }
}
