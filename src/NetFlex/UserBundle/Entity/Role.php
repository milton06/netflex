<?php

namespace NetFlex\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NetFlex\UserBundle\Entity\User;

/**
 * Role
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="NetFlex\UserBundle\Repository\RoleRepository")
 */
class Role implements RoleInterface
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
	 * @ORM\ManyToOne(targetEntity="Role", inversedBy="children", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private $parentId;
	
	/**
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="parentId")
	 */
	private $children;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
	 */
	private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    private $displayName;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    private $isAdmin;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
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
		$this->children = new ArrayCollection();
		$this->users = new ArrayCollection();
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
	 * Set parentId
	 *
	 * @param Role $parentId
	 *
	 * @return Role
	 */
	public function setParentId(Role $parentId = null)
	{
		$this->parentId = $parentId;
		
		return $this;
	}
	
	/**
	 * Get parentId
	 *
	 * @return Role
	 */
	public function getParentId()
	{
		return $this->parentId;
	}
	
	/**
	 * Add child
	 *
	 * @param Role $child
	 *
	 * @return Role
	 */
	public function addChild(Role $child)
	{
		$child->setParentId($this);
		
		$this->children[] = $child;
		
		return $this;
	}
	
	/**
	 * Remove child
	 *
	 * @param Role $child
	 */
	public function removeChild(Role $child)
	{
		$child->setParentId(null);
		
		$this->children->removeElement($child);
	}
	
	/**
	 * Get children
	 *
	 * @return ArrayCollection
	 */
	public function getChildren()
	{
		return $this->children;
	}
	
	/**
	 * Add user
	 *
	 * @param User $user
	 *
	 * @return Role
	 */
	public function addUser(User $user)
	{
		$this->users[] = $user;
		
		return $this;
	}
	
	/**
	 * Remove user
	 *
	 * @param User $user
	 */
	public function removeUser(User $user)
	{
		$this->users->removeElement($user);
	}
	
	/**
	 * Get users
	 *
	 * @return ArrayCollection
	 */
	public function getUsers()
	{
		return $this->users;
	}

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
	
	/**
	 * @inheritdoc
	 */
    public function getRole()
	{
		return $this->name;
	}

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return Role
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     *
     * @return Role
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Role
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

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Role
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
     * @return Role
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
     * @return Role
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
     * @return Role
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
    
    public function __toString()
    {
	    return $this->displayName;
    }
}
