<?php

namespace NetFlex\DeliveryChargeBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\DeliveryChargeBundle\Form\DeliveryModeTimelineType;
use NetFlex\DeliveryChargeBundle\Form\EventSubscriber\CheckDeliverabilityFormEventSubscriber;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;

class CheckDeliverabilityType extends DeliveryModeTimelineType
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
	    $builder->add('deliveryModeId', null, [
	    	'placeholder' => false,
		    'attr' => [
		    	'class' => 'delivery-modes'
		    ],
	    	'expanded' => true,
		    'multiple' => false,
	    ]);
	    
	    $builder->addEventSubscriber(new CheckDeliverabilityFormEventSubscriber($this->em));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => DeliveryModeTimeline::class,
        ]);
    }
}
