<?php

namespace NetFlex\ShipmentTrackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\ShipmentTrackBundle\Entity\OrderShipmentTrackRecord;
use NetFlex\ShipmentTrackBundle\Form\OrderShipmentTrackRecordType;

class OrderShipmentTrackRecordAddType extends OrderShipmentTrackRecordType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('trackStatusId', null, [
	    	'placeholder' => '-Select A Status-',
	    ])
	            ->add('remark');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderShipmentTrackRecord::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'netflex_shipmenttrackbundle_ordershipmenttrackrecord_add';
    }


}
