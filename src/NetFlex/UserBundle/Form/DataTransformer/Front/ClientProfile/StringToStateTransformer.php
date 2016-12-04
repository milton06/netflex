<?php
namespace NetFlex\UserBundle\Form\DataTransformer\Front\ClientProfile;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\LocationBundle\Entity\State;

class StringToStateTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function transform($state)
	{
		if (null === $state) {
			return $this->em->getRepository('NetFlexLocationBundle:State')->findOneBy(['countryId' => 1, 'id' => 41])->getName();
		}
		
		return $state->getName();
	}
	
	public function reverseTransform($stateId)
	{
		if (! $stateId) {
			return null;
		}
		if (! is_numeric($stateId)) {
			$state = $this->em->getRepository('NetFlexLocationBundle:State')->findOneByName($stateId);
		} else {
			$state = $this->em->getRepository('NetFlexLocationBundle:State')->findOneById($stateId);
		}
		
		return $state;
	}
}
