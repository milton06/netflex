<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\OrderBundle\Entity\Item;
use NetFlex\OrderBundle\Form\ItemType;
use NetFlex\OrderBundle\Form\EventSubscriber\OrderItemForClientFromDashboardFormEventSubscriber;

class OrderItemForClientFromDashboardType extends ItemType
{
    private $em;
	private $request;
	
	public function __construct(EntityManager $em, RequestStack $requestStack)
	{
		$this->em = $em;
		$this->request = $requestStack->getCurrentRequest();
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('itemBaseWeight')
	    ->add('itemDescription', HiddenType::class)
	    ->add('itemAccountableExtraWeight', HiddenType::class);
	
	    $builder->addEventSubscriber(new OrderItemForClientFromDashboardFormEventSubscriber($this->em, $this->request));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Item::class,
        ));
    }
}
