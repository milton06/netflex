<?php
namespace NetFlexUserBundle\Form\DataTransformer;

use NetFlexLocationBundle\Entity\State;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class StateToNumberTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * Transforms an object (state) to a string (number).
	 *
	 * @param  State|null $state
	 * @return string
	 */
	public function transform($state)
	{
		if (null === $state) {
			return '';
		}
		
		return $state->getId();
	}
	
	/**
	 * Transforms a string (number) to an object (state).
	 *
	 * @param  string $stateId
	 * @return State|null
	 */
	public function reverseTransform($stateId)
	{
		if (! $stateId) {
			return;
		}
		
		$state = $this->em->getRepository('NetFlexLocationBundle:State')->findOneBy(['id' => $stateId, 'status' => 1]);
		
		return $state;
	}
}
