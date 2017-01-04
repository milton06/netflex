<?php

namespace NetFlex\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NetFlex\UserBundle\Entity\User;
use NetFlex\OrderBundle\Entity\Item;
use NetFlex\OrderBundle\Entity\Price;
use NetFlex\OrderBundle\Entity\Address;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;

/**
 * OrderTransaction
 *
 * @ORM\Table(name="order_transactions")
 * @ORM\Entity(repositoryClass="NetFlex\OrderBundle\Repository\OrderTransactionRepository")
 * @UniqueEntity(
 *     {"id", "awbNumber", "invoiceNumber"},
 *     message="Duplicate entry found"
 * )
 */
class OrderTransaction
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
	 * @ORM\ManyToOne(targetEntity="\NetFlex\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $userId;
	
	/**
	 * @ORM\OneToOne(targetEntity="Item", mappedBy="orderId", cascade={"persist"})
	 *
	 * @Assert\Valid
	 */
	private $orderItem;
	
	/**
	 * @ORM\OneToOne(targetEntity="Price", mappedBy="orderId", cascade={"persist"})
	 *
	 * @Assert\Valid
	 */
	private $orderPrice;
	
	/**
	 * @ORM\OneToOne(targetEntity="Address", mappedBy="orderId", cascade={"persist"})
	 *
	 * @Assert\Valid
	 */
	private $orderAddress;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge")
	 * @ORM\JoinColumn(name="delivery_charge_id", referencedColumnName="id")
	 *
	 * @Assert\Valid
	 */
	private $deliveryChargeId;

    /**
     * @var string
     *
     * @ORM\Column(name="awb_number", type="string", length=255)
     */
    private $awbNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=255, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="order_status", type="smallint")
     */
    private $orderStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_status", type="smallint")
     */
    private $paymentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="order_status_change_remark", type="text", nullable=true)
     */
    private $orderStatusChangeRemark;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="payment_status_change_remark", type="text", nullable=true)
	 */
	private $paymentStatusChangeRemark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    private $createdOn;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
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
     * @ORM\Column(name="last_modified_by", type="integer", nullable=true)
     */
    private $lastModifiedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="modification_reason", type="text", nullable=true)
     */
    private $modificationReason;


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
	 * @return OrderTransaction
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
	 * Set orderItem
	 *
	 * @param Item $orderItem
	 *
	 * @return OrderTransaction
	 */
	public function setOrderItem(Item $orderItem = null)
	{
		$this->orderItem = $orderItem;
		
		return $this;
	}
	
	/**
	 * Get orderItem
	 *
	 * @return Item
	 */
	public function getOrderItem()
	{
		return $this->orderItem;
	}
	
	/**
	 * Set orderPrice
	 *
	 * @param Price $orderPrice
	 *
	 * @return OrderTransaction
	 */
	public function setOrderPrice(Price $orderPrice = null)
	{
		$this->orderPrice = $orderPrice;
		
		return $this;
	}
	
	/**
	 * Get orderPrice
	 *
	 * @return Price
	 */
	public function getOrderPrice()
	{
		return $this->orderPrice;
	}
	
	/**
	 * Set orderAddress
	 *
	 * @param Address $orderAddress
	 *
	 * @return OrderTransaction
	 */
	public function setOrderAddress(Address $orderAddress = null)
	{
		$this->orderAddress = $orderAddress;
		
		return $this;
	}
	
	/**
	 * Get orderAddress
	 *
	 * @return Address
	 */
	public function getOrderAddress()
	{
		return $this->orderAddress;
	}
	
	/**
	 * Set deliveryChargeId
	 *
	 * @param DeliveryCharge $deliveryChargeId
	 *
	 * @return OrderTransaction
	 */
	public function setDeliveryChargeId(DeliveryCharge $deliveryChargeId = null)
	{
		$this->deliveryChargeId = $deliveryChargeId;
		
		return $this;
	}
	
	/**
	 * Get deliveryChargeId
	 *
	 * @return DeliveryCharge
	 */
	public function getDeliveryChargeId()
	{
		return $this->deliveryChargeId;
	}

    /**
     * Set awbNumber
     *
     * @param string $awbNumber
     *
     * @return OrderTransaction
     */
    public function setAwbNumber($awbNumber)
    {
        $this->awbNumber = $awbNumber;

        return $this;
    }

    /**
     * Get awbNumber
     *
     * @return string
     */
    public function getAwbNumber()
    {
        return $this->awbNumber;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     *
     * @return OrderTransaction
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Set orderStatus
     *
     * @param integer $orderStatus
     *
     * @return OrderTransaction
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return int
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set paymentStatus
     *
     * @param integer $paymentStatus
     *
     * @return OrderTransaction
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return int
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set orderStatusChangeRemark
     *
     * @param string $orderStatusChangeRemark
     *
     * @return OrderTransaction
     */
    public function setOrderStatusChangeRemark($orderStatusChangeRemark)
    {
        $this->orderStatusChangeRemark = $orderStatusChangeRemark;

        return $this;
    }

    /**
     * Get orderStatusChangeRemark
     *
     * @return string
     */
    public function getOrderStatusChangeRemark()
    {
        return $this->orderStatusChangeRemark;
    }
	
	/**
	 * Set paymentStatusChangeRemark
	 *
	 * @param string $paymentStatusChangeRemark
	 *
	 * @return OrderTransaction
	 */
	public function setPaymentStatusChangeRemark($paymentStatusChangeRemark)
	{
		$this->paymentStatusChangeRemark = $paymentStatusChangeRemark;
		
		return $this;
	}
	
	/**
	 * Get paymentStatusChangeRemark
	 *
	 * @return string
	 */
	public function getPaymentStatusChangeRemark()
	{
		return $this->paymentStatusChangeRemark;
	}

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return OrderTransaction
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
     * @return OrderTransaction
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
     * @return OrderTransaction
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
     * @return OrderTransaction
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
     * Set modificationReason
     *
     * @param string $modificationReason
     *
     * @return OrderTransaction
     */
    public function setModificationReason($modificationReason)
    {
        $this->modificationReason = $modificationReason;

        return $this;
    }

    /**
     * Get modificationReason
     *
     * @return string
     */
    public function getModificationReason()
    {
        return $this->modificationReason;
    }
}
