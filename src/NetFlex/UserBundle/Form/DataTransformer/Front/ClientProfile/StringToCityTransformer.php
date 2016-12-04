<?php
namespace NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\LocationBundle\Entity\City;

class StringToCityTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function transform($city)
	{
		if (null === $city) {
			return $this->em->getRepository('NetFlexLocationBundle:City')->findOneBy(['countryId' => 1, 'stateId' => 41, 'id' => 5583])->getName();
		}
		
		return $city->getName();
	}
	
	public function reverseTransform($cityId)
	{
		if (! $cityId) {
			return null;
		}
		if (! is_numeric($cityId)) {
			$city = $this->em->getRepository('NetFlexLocationBundle:City')->findOneByName($cityId);
		} else {
			$city = $this->em->getRepository('NetFlexLocationBundle:City')->findOneById($cityId);
		}
		
		return $city;
	}
}
