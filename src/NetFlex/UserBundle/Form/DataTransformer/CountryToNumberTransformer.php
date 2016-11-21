<?php
namespace NetFlexUserBundle\Form\DataTransformer;

use NetFlexLocationBundle\Entity\Country;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class CountryToNumberTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * Transforms an object (country) to a string (number).
	 *
	 * @param  Country|null $country
	 * @return string
	 */
	public function transform($country)
	{
		if (null === $country) {
			return '';
		}
		
		return $country->getId();
	}
	
	/**
	 * Transforms a string (number) to an object (country).
	 *
	 * @param  string $countryId
	 * @return Country|null
	 */
	public function reverseTransform($countryId)
	{
		if (! $countryId) {
			return;
		}
		
		$country = $this->em->getRepository('NetFlexLocationBundle:Country')->findOneBy(['id' => $countryId, 'status' => 1]);
		
		return $country;
	}
}
