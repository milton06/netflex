<?php

namespace NetFlex\UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NetFlex\UserBundle\Entity\Role;
use NetFlex\UserBundle\Entity\Address;
use NetFlex\UserBundle\Entity\Contact;
use NetFlex\UserBundle\Entity\Email;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="NetFlex\UserBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
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
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
	 * @ORM\JoinTable(name="user_roles")
	 */
	private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="mid_name", type="string", length=255)
     */
    private $midName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;
	
	/**
	 * @ORM\OneToMany(targetEntity="Address", mappedBy="userId")
	 */
	private $addresses;
	
	/**
	 * @ORM\OneToMany(targetEntity="Contact", mappedBy="userId")
	 */
	private $contacts;
	
	/**
	 * @ORM\OneToMany(targetEntity="Email", mappedBy="userId")
	 */
	private $emails;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    private $createdOn;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified_on", type="datetime")
     */
    private $lastModifiedOn;

    /**
     * @var int
     *
     * @ORM\Column(name="last_modified_by", type="integer")
     */
    private $lastModifiedBy;
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->roles = new ArrayCollection();
		$this->addresses = new ArrayCollection();
		$this->contacts = new ArrayCollection();
		$this->emails = new ArrayCollection();
	}
	
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
	 * Add role
	 *
	 * @param Role $role
	 *
	 * @return User
	 */
	public function addRole(Role $role)
	{
		$this->roles[] = $role;
		
		return $this;
	}
	
	/**
	 * Remove role
	 *
	 * @param Role $role
	 */
	public function removeRole(Role $role)
	{
		$this->roles->removeElement($role);
	}
	
	/**
	 * @inheritdoc
	 */
	public function getRoles()
	{
		$roles = [];
		
		if (empty($this->roles)) {
			return ['ROLE_GUEST'];
		}
		
		foreach ($this->roles as $thisRole) {
			$roles[] = $thisRole->getName();
		}
		
		return $roles;
	}

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set midName
     *
     * @param string $midName
     *
     * @return User
     */
    public function setMidName($midName)
    {
        $this->midName = $midName;

        return $this;
    }

    /**
     * Get midName
     *
     * @return string
     */
    public function getMidName()
    {
        return $this->midName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
	
	/**
	 * Add address
	 *
	 * @param Address $address
	 *
	 * @return User
	 */
	public function addAddress(Address $address)
	{
		$address->setUserId($this);
		
		$this->addresses[] = $address;
		
		return $this;
	}
	
	/**
	 * Remove address
	 *
	 * @param Address $address
	 */
	public function removeAddress(Address $address)
	{
		$address->setUserId(null);
		
		$this->addresses->removeElement($address);
	}
	
	/**
	 * Get addresses
	 *
	 * @return ArrayCollection
	 */
	public function getAddresses()
	{
		return $this->addresses;
	}
	
	/**
	 * Add contact
	 *
	 * @param Contact $contact
	 *
	 * @return User
	 */
	public function addContact(Contact $contact)
	{
		$contact->setUserId($this);
		
		$this->contacts[] = $contact;
		
		return $this;
	}
	
	/**
	 * Remove contact
	 *
	 * @param Contact $contact
	 */
	public function removeContact(Contact $contact)
	{
		$contact->setUserId(null);
		
		$this->contacts->removeElement($contact);
	}
	
	/**
	 * Get contacts
	 *
	 * @return ArrayCollection
	 */
	public function getContacts()
	{
		return $this->contacts;
	}
	
	/**
	 * Add email
	 *
	 * @param Email $email
	 *
	 * @return User
	 */
	public function addEmail(Email $email)
	{
		$email->setUserId($this);
		
		$this->emails[] = $email;
		
		return $this;
	}
	
	/**
	 * Remove email
	 *
	 * @param Email $email
	 */
	public function removeEmail(Email $email)
	{
		$email->setUserId(null);
		
		$this->emails->removeElement($email);
	}
	
	/**
	 * Get emails
	 *
	 * @return ArrayCollection
	 */
	public function getEmails()
	{
		return $this->emails;
	}

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return User
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return User
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set lastModifiedOn
     *
     * @param \DateTime $lastModifiedOn
     *
     * @return User
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get lastModifiedOn
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set lastModifiedBy
     *
     * @param integer $lastModifiedBy
     *
     * @return User
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get lastModifiedBy
     *
     * @return int
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }
	
	/**
	 * @inheritdoc
	 */
    public function getSalt()
	{
		return null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function eraseCredentials()
	{
		//
	}
	
	/**
	 * @inheritdoc
	 */
	public function isAccountNonExpired()
	{
		return $this->status;
	}
	
	/**
	 * @inheritdoc
	 */
	public function isAccountNonLocked()
	{
		return $this->status;
	}
	
	/**
	 * @inheritdoc
	 */
	public function isCredentialsNonExpired()
	{
		return $this->status;
	}
	
	/**
	 * @inheritdoc
	 */
	public function isEnabled()
	{
		return $this->status;
	}
	
	/**
	 * @inheritdoc
	 */
	public function serialize()
	{
		return serialize([
			$this->id,
			$this->username,
			$this->firstName,
			$this->midName,
			$this->lastName,
			$this->status,
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function unserialize($serialized)
	{
		list(
			$this->id,
			$this->username,
			$this->firstName,
			$this->midName,
			$this->lastName,
			$this->status
		) = unserialize($serialized);
	}
	
	public function __toString()
	{
		return '';
	}
}
