<?php

namespace NetFlex\LocationBundle\Form\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CityEditTypeEventSubscriber implements EventSubscriberInterface
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }
    
    public function preSubmit(FormEvent $formEvent)
    {
        $form = $formEvent->getForm();
        $formData = $formEvent->getData();
    
        $form->add('stateId', EntityType::class, [
            'placeholder' => '-Select A State-',
            'class' => 'NetFlexLocationBundle:State',
            'query_builder' => function(EntityRepository $er) use($formData) {
                if ($formData['countryId']) {
                    return $er->createQueryBuilder('STATE')
                    ->where('STATE.countryId = ' . $formData['countryId'])
                    ->andWhere('STATE.id not in (42, 43, 44, 45, 46, 47)')
                    ->andWhere('STATE.status = 1');
                } else {
                    return null;
                }
            },
            'data' => ($formData['stateId']) ? $this->em->getReference('NetFlexLocationBundle:State', ['id' => $formData['stateId']]) : null,
        ]);
    }
}
