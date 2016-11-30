<?php
namespace NetFlex\UserBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\UserBundle\Entity\Email;

class StringToEmailTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * @param  mixed $email
	 *
	 * @return string
	 */
	public function transform($email)
	{
		if (! $email instanceof Email) {
			/**
			 * If $email is anything but an instance of the Email entity class.
			 */
			return '';
		}
		
		return $email->getEmail();
	}
	
	/**
	 * @param  string $emailId
	 *
	 * @return Email|null
	 *
	 * @throws TransformationFailedException
	 */
	public function reverseTransform($emailId)
	{
		if (! $emailId) {
			/**
			 * No email ID was provided by user.
			 */
			return null;
		}
		
		/**
		 * Search for an existing email.
		 */
		$email = $this->em->getRepository('NetFlexUserBundle:Email')->findOneBy(['email' => $emailId, 'status' => 1]);
		if ($email) {
			/**
			 * Such an email already exists in DB.
			 */
			throw new TransformationFailedException("This email $emailId is already taken");
		}
		
		/**
		 * Construct a new Email object.
		 */
		$email = new Email();
		$email->setEmail($emailId);
		$email->setIsPrimary(1);
		$email->setStatus(1);
		
		return $email;
	}
}
