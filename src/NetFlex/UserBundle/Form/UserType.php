<?php

namespace NetFlex\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use NetFlex\UserBundle\Form\AddressType;
use NetFlex\UserBundle\Form\ContactType;
use NetFlex\UserBundle\Form\EmailType;
use NetFlex\UserBundle\Entity\User;

class UserType extends AbstractType
{
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('username')
            ->add('password', PasswordType::class, [
            	'always_empty' => false,
            ])
            ->add('firstName')
            ->add('midName')
            ->add('lastName')
            ->add('addresses', CollectionType::class, [
            	'entry_type' => AddressType::class,
	            'allow_add' => true,
	            'allow_delete' => true,
	            'delete_empty' => true,
	            'by_reference' => false,
            ])
            ->add('contacts', CollectionType::class, [
            	'entry_type' => ContactType::class,
	            'allow_add' => true,
	            'allow_delete' => true,
	            'delete_empty' => true,
	            'by_reference' => false,
            ])
            ->add('emails', CollectionType::class, [
            	'entry_type' => EmailType::class,
	            'allow_add' => true,
	            'allow_delete' => true,
	            'delete_empty' => true,
	            'by_reference' => false,
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => User::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'netflex_userbundle_user';
    }
}
