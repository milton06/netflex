<?php
namespace NetFlex\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NetFlex\MailerBundle\Entity\MailTemplate;

class MailTemplateEditForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('typeKey')
		->add('typeName')
		->add('sentFromEmail', EmailType::class)
		->add('sentFromName')
		->add('subject')
		->add('body', TextareaType::class);
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => MailTemplate::class,
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'netflex_mailtemplate_edit';
	}
}
