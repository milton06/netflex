<?php

namespace NetFlex\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ItemType
 *
 * @ORM\Table(name="order_item_types")
 * @ORM\Entity(repositoryClass="NetFlex\OrderBundle\Repository\ItemTypeRepository")
 */
class ItemType
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
	 * @ORM\ManyToOne(targetEntity="ItemType", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private $parentId;
	
	/**
	 * @ORM\OneToMany(targetEntity="ItemType", mappedBy="parentId")
	 */
	private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="item_type_name", type="string", length=255)
     */
    private $itemTypeName;

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
	 * @param ItemType $parentId
	 *
	 * @return ItemType
	 */
	public function setParentId(ItemType $parentId = null)
	{
		$this->parentId = $parentId;
		
		return $this;
	}
	
	/**
	 * Get parentId
	 *
	 * @return ItemType
	 */
	public function getParentId()
	{
		return $this->parentId;
	}
	
	/**
	 * Add child
	 *
	 * @param ItemType $child
	 *
	 * @return ItemType
	 */
	public function addChild(ItemType $child)
	{
		$child->setParentId($this);
		
		$this->children[] = $child;
		
		return $this;
	}
	
	/**
	 * Remove child
	 *
	 * @param ItemType $child
	 */
	public function removeChild(ItemType $child)
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
     * Set itemTypeName
     *
     * @param string $itemTypeName
     *
     * @return ItemType
     */
    public function setItemTypeName($itemTypeName)
    {
        $this->itemTypeName = $itemTypeName;

        return $this;
    }

    /**
     * Get itemTypeName
     *
     * @return string
     */
    public function getItemTypeName()
    {
        return $this->itemTypeName;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ItemType
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
     * @return ItemType
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
     * @return ItemType
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
     * @return ItemType
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
     * @return ItemType
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
}
