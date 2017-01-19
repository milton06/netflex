<?php

namespace NetFlex\LocationBundle\Form\State;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\LocationBundle\Entity\State;

class StateNewType extends AbstractType
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('countryId', EntityType::class, [
            'placeholder' => '-Select A Country-',
            'class' => 'NetFlexLocationBundle:Country',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('COUNTRY')
                ->where('COUNTRY.status = 1');
            },
            'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1, 'status' => 1]),
        ])
        ->add('name');
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => State::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'state_new_type';
    }
}
