<?php

namespace NetFlex\ShipmentTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\ShipmentTrackBundle\Entity\TrackStatus;
use NetFlex\UserBundle\Entity\User;

/**
 * OrderShipmentTrackRecord
 *
 * @ORM\Table(name="order_shipment_track_records")
 * @ORM\Entity(repositoryClass="NetFlex\ShipmentTrackBundle\Repository\OrderShipmentTrackRecordRepository")
 *
 * @UniqueEntity(
 *     fields={"trackStatusId"},
 *     message="An entry for this order track status already exists"
 * )
 */
class OrderShipmentTrackRecord
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
	 * @ORM\ManyToOne(targetEntity="\NetFlex\OrderBundle\Entity\OrderTransaction")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $orderId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="TrackStatus")
	 * @ORM\JoinColumn(name="track_status_id", referencedColumnName="id")
	 *
	 * @Assert\NotBlank(
	 *     message="Track status is required"
	 * )
	 */
	private $trackStatusId;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text")
     *
     * @Assert\NotBlank(
     *     message="A remark is required"
     * )
     */
    private $remark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    private $createdOn;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\NetFlex\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
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
     * @ORM\ManyToOne(targetEntity="\NetFlex\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id")
     */
    private $lastModifiedBy;


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
	 * @return OrderShipmentTrackRecord
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
	 * Set trackStatusId
	 *
	 * @param TrackStatus $trackStatusId
	 *
	 * @return OrderShipmentTrackRecord
	 */
	public function setTrackStatusId(TrackStatus $trackStatusId = null)
	{
		$this->trackStatusId = $trackStatusId;
		
		return $this;
	}
	
	/**
	 * Get trackStatusId
	 *
	 * @return TrackStatus
	 */
	public function getTrackStatusId()
	{
		return $this->trackStatusId;
	}

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return OrderShipmentTrackRecord
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return OrderShipmentTrackRecord
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
	 * @param User $createdBy
	 *
	 * @return OrderShipmentTrackRecord
	 */
	public function setCreatedBy(User $createdBy = null)
	{
		$this->createdBy = $createdBy;
		
		return $this;
	}
	
	/**
	 * Get createdBy
	 *
	 * @return User
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
     * @return OrderShipmentTrackRecord
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
     * @param User $lastModifiedBy
     *
     * @return OrderShipmentTrackRecord
     */
    public function setLastModifiedBy(User $lastModifiedBy = null)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get lastModifiedBy
     *
     * @return User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }
}
