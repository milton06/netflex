<?php

namespace NetFlex\LocationBundle\Form\City;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\LocationBundle\Entity\City;
use NetFlex\LocationBundle\Form\EventSubscriber\CityNewTypeEventSubscriber;

class CityNewType extends AbstractType
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
            },
            'data' => $this->em->getReference('NetFlexLocationBundle:Country', ['id' => 1]),
        ])
        ->add('stateId', EntityType::class, [
            'placeholder' => '-Select A State-',
            'class' => 'NetFlexLocationBundle:State',
            'query_builder' => function(EntityRepository $er) use($formData) {
                return $er->createQueryBuilder('STATE')
                ->where('STATE.countryId = 1')
                ->andWhere('STATE.id not in (42, 43, 44, 45, 46, 47)')
                ->andWhere('STATE.status = 1');
            },
            'data' => $this->em->getReference('NetFlexLocationBundle:State', ['countryId' => 1, 'id' => 41]),
        ])
        ->add('name');
        
        $builder->addEventSubscriber(new CityNewTypeEventSubscriber($this->em));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'city_new_type';
    }
}
