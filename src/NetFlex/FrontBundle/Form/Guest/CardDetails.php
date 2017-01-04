<?php

namespace NetFlex\FrontBundle\Form\Guest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardDetails extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('paymentModes', ChoiceType::class, [
			'placeholder' => false,
			'expanded' => true,
			'choices' => $options['paymentModes'],
		])
		->add('dcTypes', ChoiceType::class, [
			'placeholder' => '-Select A Debit Card Type-',
			'choices' => $options['dcTypes'],
		])
		->add('key', HiddenType::class, [
			'data' => $options['key'],
		])
		->add('txnid', HiddenType::class, [
			'data' => $options['txnid'],
		])
		->add('amount', HiddenType::class, [
			'data' => $options['amount'],
		])
		->add('productinfo', HiddenType::class, [
			'data' => $options['productinfo'],
		])
		->add('firstname', HiddenType::class, [
			'data' => $options['firstname'],
		])
		->add('email', HiddenType::class, [
			'data' => $options['email'],
		])
		->add('phone', HiddenType::class, [
			'data' => $options['phone'],
		])
		->add('surl', HiddenType::class, [
			'data' => $options['surl'],
		])
		->add('furl', HiddenType::class, [
			'data' => $options['furl'],
		])
		->add('curl', HiddenType::class, [
			'data' => $options['curl'],
		])
		->add('HASH', HiddenType::class, [
			'data' => $options['HASH'],
		])
		->add('pg', HiddenType::class, [
			'data' => $options['pg'],
		])
		->add('ccnum', TextType::class)
		->add('ccname', TextType::class)
		->add('ccvv', TextType::class)
		->add('ccexpmon', ChoiceType::class, [
			'placeholder' => '-Select A Month',
			'choices' => $options['months'],
		])
		->add('ccexpyr', ChoiceType::class, [
			'placeholder' => 'Select A Year',
			'choices' => $options['years'],
		])
		->add('bankcode', HiddenType::class, [
			'data' => $options['bankcode'],
		]);
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
			'paymentModes' => [],
			'dcTypes' => [],
			'key' => '',
			'txnid' => '',
			'amount' => '',
			'productinfo' => '',
			'firstname' => '',
			'email' => '',
			'phone' => '',
			'surl' => '',
			'furl' => '',
			'curl' => '',
			'HASH' => '',
			'pg' => '',
			'bankcode' => '',
			'months' => [],
			'years' => [],
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'guest_book_shipment_payment_card_details';
	}
}
