<?php

namespace NetFlex\StaticPageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticPageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['action'])
        ->setMethod('POST')
        ->add('searchTitle', TextType::class, [
            'data' => $options['searchTitle'],
        ])
        ->add('searchSlug', TextType::class, [
            'data' => $options['searchSlug'],
        ])
        ->add('searchStatus', ChoiceType::class, [
            'placeholder' => '-All Statuses-',
            'choices' => $options['searchStatuses'],
            'data' => $options['searchStatus'],
        ])
        ->add('searchFromDate', TextType::class, [
            'data' => $options['searchFromDate'],
        ])
        ->add('searchToDate', TextType::class, [
            'data' => $options['searchToDate'],
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'action' => '',
            'searchTitle' => '',
            'searchSlug' => '',
            'searchStatus' => '',
            'searchStatuses' => [],
            'searchFromDate' => '',
            'searchToDate' => '',
        ]);
    }
    
    public function getBlockPrefix()
    {
        return '';
    }
}
