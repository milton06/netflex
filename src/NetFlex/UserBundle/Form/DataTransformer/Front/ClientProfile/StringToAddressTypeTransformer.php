<?php
namespace NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\UserBundle\Entity\AddressType;

class StringToAddressTypeTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function transform($addressType)
	{
		if (null === $addressType) {
			return '';
		}
		
		return $addressType->getId();
	}
	
	public function reverseTransform($addressTypeId)
	{
		if (! $addressTypeId) {
			return null;
		}
		$addressType = $this->em->getRepository('NetFlexUserBundle:AddressType')->findOneById($addressTypeId);
		
		return $addressType;
	}
}
