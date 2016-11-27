<?php

namespace NetFlex\OrderBundle\Form;

use Doctrine\ORM\EntityManager;
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
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('itemBaseWeight')
	    ->add('itemDescription', HiddenType::class)
	    ->add('itemAccountableExtraWeight', HiddenType::class)
	    ->add('itemVolumetricBaseWeight', HiddenType::class)
	    ->add('itemVolumetricAccountableExtraWeight', HiddenType::class);
	    
	    $builder->addEventSubscriber(new OrderItemForClientFromDashboardFormEventSubscriber($this->em));
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
