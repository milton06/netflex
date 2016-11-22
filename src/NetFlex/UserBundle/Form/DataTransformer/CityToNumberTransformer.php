<?php
namespace NetFlexUserBundle\Form\DataTransformer;

use NetFlexLocationBundle\Entity\City;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class CityToNumberTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * Transforms an object (city) to a string (number).
	 *
	 * @param  City|null $city
	 * @return string
	 */
	public function transform($city)
	{
		if (null === $city) {
			return '';
		}
		
		return $city->getId();
	}
	
	/**
	 * Transforms a string (number) to an object (city).
	 *
	 * @param  string $cityId
	 * @return City|null
	 */
	public function reverseTransform($cityId)
	{
		if (! $cityId) {
			return;
		}
		
		$city = $this->em->getRepository('NetFlexLocationBundle:City')->findOneBy(['id' => $cityId, 'status' => 1]);
		
		return $city;
	}
}
