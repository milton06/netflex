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
		$builder->add('key', HiddenType::class, [
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
			'choices' => [
				'JAN' => 1,
				'FEB' => 2,
				'MAR' => 3,
				'APR' => 4,
				'MAY' => 5,
				'JUN' => 6,
				'JUL' => 7,
				'AUG' => 8,
				'SEP' => 9,
				'OCT' => 10,
				'NOV' => 11,
				'DEC' => 12,
			],
		])
		->add('ccexpyr', ChoiceType::class, [
			'placeholder' => 'Select A Year',
			'choices' => $options['expYear'],
		])
		->add('bankcode', HiddenType::class);
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
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
			'HASH' => '',
			'pg' => '',
			'expYear' => [],
		]);
	}
	
	public function getBlockPrefix()
	{
		return 'guest_book_shipment_payment_card_details';
	}
}
