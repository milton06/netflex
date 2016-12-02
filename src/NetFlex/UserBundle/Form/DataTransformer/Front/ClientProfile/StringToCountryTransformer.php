<?php
namespace NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\LocationBundle\Entity\Country;

class StringToCountryTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function transform($country)
	{
		if (null === $country) {
			return '';
		}
		
		return $country->getName();
	}
	
	public function reverseTransform($countryId)
	{
		if (! $countryId) {
			return null;
		}
		if (! is_numeric($countryId)) {
			$country = $this->em->getRepository('NetFlexLocationBundle:Country')->findOneByName($countryId);
		} else {
			$country = $this->em->getRepository('NetFlexLocationBundle:Country')->findOneById($countryId);
		}
		
		return $country;
	}
}
