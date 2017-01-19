<?php

namespace NetFlex\LocationBundle\Form\State;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\LocationBundle\Entity\State;

class StateEditType extends AbstractType
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formData = $builder->getData();
        
        $builder->add('countryId', EntityType::class, [
            'placeholder' => '-Select A Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('COUNTRY')
                ->where('COUNTRY.status = 1');
            }
        ])
        ->add('name')
        ->add('status');
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => State::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'state_edit_type';
    }
}
