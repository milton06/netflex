<?php

namespace NetFlex\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NetFlex\UserBundle\Entity\User;

/**
 * Contact
 *
 * @ORM\Table(name="contacts")
 * @ORM\Entity(repositoryClass="NetFlex\UserBundle\Repository\ContactRepository")
 */
class Contact
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
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="contacts", cascade={"persist"})
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_number", type="string", length=255)
     */
    private $contactNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_primary", type="boolean")
     */
    private $isPrimary;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;


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
	 * Set userId
	 *
	 * @param User $userId
	 *
	 * @return Contact
	 */
	public function setUserId(User $userId = null)
	{
		$this->userId = $userId;
		
		return $this;
	}
	
	/**
	 * Get userId
	 *
	 * @return User
	 */
	public function getUserId()
	{
		return $this->userId;
	}

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     *
     * @return Contact
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return Contact
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return bool
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Contact
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}
