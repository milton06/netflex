<?php
namespace NetFlex\OrderBundle\Form\DataTransformer;

use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToDeliveryChargeTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function transform($deliveryCharge)
	{
		return '';
	}
	
	public function reverseTransform($deliveryChargeId)
	{
		if (! $deliveryChargeId) {
			return;
		}
	
		$deliveryCharge = $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findOneById($deliveryChargeId);
		
		return $deliveryCharge;
	}
}
