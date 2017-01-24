<?php

namespace NetFlex\StaticPageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\StaticPageBundle\Entity\StaticPage;
use NetFlex\StaticPageBundle\Form\StaticPageSectionNewType;

class StaticPageNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
        ->add('slug')
        ->add('metaKeyword', TextareaType::class)
        ->add('metaDescription', TextareaType::class)
        ->add('staticPageSections', CollectionType::class, [
            'entry_type' => StaticPageSectionNewType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StaticPage::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'dashboard_static_page_new';
    }
}
