<?php
namespace NetFlex\OrderBundle\Form\DataTransformer;

use NetFlex\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToUserTransformer implements DataTransformerInterface
{
	private $userId;
	private $em;

	public function __construct($userId, EntityManager $em)
	{
		$this->userId = $userId;
		$this->em = $em;
	}

	public function transform($user)
	{
		return $this->userId;
	}
	
	public function reverseTransform($userId)
	{
		if (! $userId) {
			return;
		}
	
		$user = $this->em->getRepository('NetFlexUserBundle:User')->findOneById($userId);
		
		return $user;
	}
}
