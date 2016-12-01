<?php
namespace NetFlex\UserBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use NetFlex\UserBundle\Entity\Contact;

class StringToContactTransformer implements DataTransformerInterface
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * @param  mixed $contact
	 *
	 * @return string
	 */
	public function transform($contact)
	{
		if (! $contact instanceof Contact) {
			return '';
		}
		
		return $contact->getContactNumber();
	}
	
	/**
	 * @param  string $contactNumber
	 *
	 * @return null|Contact
	 */
	public function reverseTransform($contactNumber)
	{
		if (! $contactNumber) {
			/**
			 * No contact number was provided by user.
			 */
			return null;
		}
		
		/**
		 * Construct a new Contact object.
		 */
		$contact = new Contact();
		$contact->setContactNumber($contactNumber);
		$contact->setIsPrimary(1);
		$contact->setStatus(1);
		
		return $contact;
	}
}
