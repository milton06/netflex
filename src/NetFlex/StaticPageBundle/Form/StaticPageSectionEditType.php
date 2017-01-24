<?php

namespace NetFlex\StaticPageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NetFlex\StaticPageBundle\Entity\StaticPageSection;

class StaticPageSectionEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', TextareaType::class)
        ->add('position', IntegerType::class)
        ->add('status');
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StaticPageSection::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'dashboard_static_page_section_edit';
    }
}
