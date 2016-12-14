<?php

namespace NetFlex\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MailTemplate
 *
 * @ORM\Table(name="mail_templates")
 * @ORM\Entity(repositoryClass="NetFlex\MailerBundle\Repository\MailTemplateRepository")
 *
 * @UniqueEntity(
 *     fields={"typeKey"},
 *     message="A mail template for the same type of mail does already exist"
 * )
 */
class MailTemplate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type_key", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please provide a mail type"
     * )
     * @Assert\Regex(
     *     pattern="/[a-z0-9_]/i",
     *     message="Mail type can contain only alphanumeric characters and underscores"
     * )
     */
    private $typeKey;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="type_name", type="string", length=255)
	 *
	 * @Assert\NotBlank(
	 *     message="Please provide a mail type name"
	 * )
	 * @Assert\Regex(
	 *     pattern="/[a-z0-9 ]/i",
	 *     message="Mail type name can contain only alphanumeric characters and spaces"
	 * )
	 */
	private $typeName;

    /**
     * @var string
     *
     * @ORM\Column(name="sent_from_email", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please provide a mail ID to send this type of mail from"
     * )
     * @Assert\Email(
     *     message="Please provide a valid mail ID"
     * )
     */
    private $sentFromEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="sent_from_name", type="string", length=255, nullable=true)
     */
    private $sentFromName;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please provide a subject line for this type of mail"
     * )
     * @Assert\Length(
     *     min=10,
     *     max=255,
     *     minMessage="Subject line should be of at-least 10 characters",
     *     maxMessage="Subject line cannot be longer than 255 characters"
     * )
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     *
     * @Assert\NotBlank(
     *     message="Please provide a content for this type of mail"
     * )
     */
    private $body;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set typeKey
     *
     * @param string $typeKey
     *
     * @return MailTemplate
     */
    public function setTypeKey($typeKey)
    {
        $this->typeKey = $typeKey;

        return $this;
    }

    /**
     * Get typeKey
     *
     * @return string
     */
    public function getTypeKey()
    {
        return $this->typeKey;
    }
	
	/**
	 * Set typeName
	 *
	 * @param string $typeName
	 *
	 * @return MailTemplate
	 */
	public function setTypeName($typeName)
	{
		$this->typeKey = $typeName;
		
		return $this;
	}
	
	/**
	 * Get typeName
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->typeName;
	}

    /**
     * Set sentFromEmail
     *
     * @param string $sentFromEmail
     *
     * @return MailTemplate
     */
    public function setSentFromEmail($sentFromEmail)
    {
        $this->sentFromEmail = $sentFromEmail;

        return $this;
    }

    /**
     * Get sentFromEmail
     *
     * @return string
     */
    public function getSentFromEmail()
    {
        return $this->sentFromEmail;
    }

    /**
     * Set sentFromName
     *
     * @param string $sentFromName
     *
     * @return MailTemplate
     */
    public function setSentFromName($sentFromName)
    {
        $this->sentFromName = $sentFromName;

        return $this;
    }

    /**
     * Get sentFromName
     *
     * @return string
     */
    public function getSentFromName()
    {
        return $this->sentFromName;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return MailTemplate
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return MailTemplate
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

