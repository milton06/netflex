<?php

namespace NetFlex\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NetFlex\OrderBundle\Entity\ItemType;
use NetFlex\DeliveryChargeBundle\Entity\WeightUnit;
use NetFlex\OrderBundle\Entity\OrderTransaction;

/**
 * Item
 *
 * @ORM\Table(name="order_items")
 * @ORM\Entity(repositoryClass="NetFlex\OrderBundle\Repository\ItemRepository")
 */
class Item
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
	 * @ORM\OneToOne(targetEntity="OrderTransaction", inversedBy="orderItem")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $orderId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ItemType")
	 * @ORM\JoinColumn(name="item_primary_type_id", referencedColumnName="id")
	 *
	 * @Assert\NotBlank(
	 *     message="Required field"
	 * )
	 */
	private $itemPrimaryTypeId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ItemType")
	 * @ORM\JoinColumn(name="item_secondary_type_id", referencedColumnName="id")
	 *
	 * @Assert\NotBlank(
	 *     message="Required field"
	 * )
	 */
	private $itemSecondaryTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="item_description", type="text", nullable=true)
     */
    private $itemDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="item_base_weight", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank(
     *     message="Required field"
     * )
     */
    private $itemBaseWeight;

    /**
     * @var string
     *
     * @ORM\Column(name="item_accountable_extra_weight", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $itemAccountableExtraWeight;

    /**
     * @var string
     *
     * @ORM\Column(name="item_user_base_weight", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $itemUserBaseWeight;

    /**
     * @var string
     *
     * @ORM\Column(name="item_user_accountable_extra_weight", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $itemUserAccountableExtraWeight;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\DeliveryChargeBundle\Entity\WeightUnit")
	 * @ORM\JoinColumn(name="item_weight_unit_id", referencedColumnName="id")
	 *
	 * @Assert\NotBlank(
	 *     message="Required field"
	 * )
	 */
	private $itemWeightUnitId;


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
	 * Set orderId
	 *
	 * @param OrderTransaction $orderId
	 *
	 * @return Item
	 */
	public function setOrderId(OrderTransaction $orderId = null)
	{
		$this->orderId = $orderId;
		
		return $this;
	}
	
	/**
	 * Get orderId
	 *
	 * @return OrderTransaction
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}
	
	/**
	 * Set itemPrimaryTypeId
	 *
	 * @param  ItemType $itemPrimaryTypeId
	 *
	 * @return Item
	 */
	public function setItemPrimaryTypeId(ItemType $itemPrimaryTypeId = null)
	{
		$this->itemPrimaryTypeId = $itemPrimaryTypeId;
		
		return $this;
	}
	
	/**
	 * Get itemPrimaryTypeId
	 *
	 * @return ItemType
	 */
	public function getItemPrimaryTypeId()
	{
		return $this->itemPrimaryTypeId;
	}
	
	/**
	 * Set itemSecondaryTypeId
	 *
	 * @param  ItemType $itemSecondaryTypeId
	 *
	 * @return Item
	 */
	public function setItemSecondaryTypeId(ItemType $itemSecondaryTypeId = null)
	{
		$this->itemSecondaryTypeId = $itemSecondaryTypeId;
		
		return $this;
	}
	
	/**
	 * Get itemSecondaryTypeId
	 *
	 * @return ItemType
	 */
	public function getItemSecondaryTypeId()
	{
		return $this->itemSecondaryTypeId;
	}

    /**
     * Set itemDescription
     *
     * @param string $itemDescription
     *
     * @return Item
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * Get itemDescription
     *
     * @return string
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * Set itemBaseWeight
     *
     * @param string $itemBaseWeight
     *
     * @return Item
     */
    public function setItemBaseWeight($itemBaseWeight)
    {
        $this->itemBaseWeight = $itemBaseWeight;

        return $this;
    }

    /**
     * Get itemBaseWeight
     *
     * @return string
     */
    public function getItemBaseWeight()
    {
        return $this->itemBaseWeight;
    }

    /**
     * Set itemAccountableExtraWeight
     *
     * @param string $itemAccountableExtraWeight
     *
     * @return Item
     */
    public function setItemAccountableExtraWeight($itemAccountableExtraWeight)
    {
        $this->itemAccountableExtraWeight = $itemAccountableExtraWeight;

        return $this;
    }

    /**
     * Get itemAccountableExtraWeight
     *
     * @return string
     */
    public function getItemAccountableExtraWeight()
    {
        return $this->itemAccountableExtraWeight;
    }

    /**
     * Set itemUserBaseWeight
     *
     * @param string $itemUserBaseWeight
     *
     * @return Item
     */
    public function setItemUserBaseWeight($itemUserBaseWeight)
    {
        $this->itemUserBaseWeight = $itemUserBaseWeight;

        return $this;
    }

    /**
     * Get itemUserBaseWeight
     *
     * @return string
     */
    public function getItemUserBaseWeight()
    {
        return $this->itemUserBaseWeight;
    }

    /**
     * Set itemUserAccountableExtraWeight
     *
     * @param string $itemUserAccountableExtraWeight
     *
     * @return Item
     */
    public function setItemUserAccountableExtraWeight($itemUserAccountableExtraWeight)
    {
        $this->itemUserAccountableExtraWeight = $itemUserAccountableExtraWeight;

        return $this;
    }

    /**
     * Get itemUserAccountableExtraWeight
     *
     * @return string
     */
    public function getItemUserAccountableExtraWeight()
    {
        return $this->itemUserAccountableExtraWeight;
    }

    /**
     * Set itemWeightUnitId
     *
     * @param WeightUnit $itemWeightUnitId
     *
     * @return Item
     */
    public function setItemWeightUnitId(WeightUnit $itemWeightUnitId = null)
    {
        $this->itemWeightUnitId = $itemWeightUnitId;

        return $this;
    }

    /**
     * Get itemWeightUnitId
     *
     * @return WeightUnit
     */
    public function getItemWeightUnitId()
    {
        return $this->itemWeightUnitId;
    }
}
