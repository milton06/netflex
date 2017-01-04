<?php

namespace NetFlex\FrontBundle\Form\DataTransformer\Guest;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;

class NumberToDeliveryChargeDataTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function transform($deliveryCharge)
	{
		if (! $deliveryCharge instanceof DeliveryCharge) {
			return null;
		}
		
		return $deliveryCharge->getId();
	}
	
	public function reverseTransform($deliveryChargeId)
	{
		return $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findOneById($deliveryChargeId);
	}
}
