<?php

namespace NetFlex\DeliveryChargeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryModeTimeline
 *
 * @ORM\Table(name="delivery_mode_timelines")
 * @ORM\Entity(repositoryClass="NetFlex\DeliveryChargeBundle\Repository\DeliveryModeTimelineRepository")
 */
class DeliveryModeTimeline
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
	 * @ORM\ManyToOne(targetEntity="Country")
	 * @ORM\JoinColumn(name="source_country_id", referencedColumnName="id", nullable=true)
	 */
	private $sourceCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="State")
	 * @ORM\JoinColumn(name="source_state_id", referencedColumnName="id", nullable=true)
	 */
	private $sourceStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="City")
	 * @ORM\JoinColumn(name="source_city_id", referencedColumnName="id", nullable=true)
	 */
	private $sourceCityId;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="source_zip_code", type="string", length=255, nullable=true)
	 */
	private $sourceZipCode;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Country")
	 * @ORM\JoinColumn(name="destination_country_id", referencedColumnName="id", nullable=true)
	 */
	private $destinationCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="State")
	 * @ORM\JoinColumn(name="destination_state_id", referencedColumnName="id", nullable=true)
	 */
	private $destinationStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="City")
	 * @ORM\JoinColumn(name="destination_city_id", referencedColumnName="id", nullable=true)
	 */
	private $destinationCityId;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="destination_zip_code", type="string", length=255, nullable=true)
	 */
	private $destinationZipCode;
	
	/**
	 * @ORM\ManyToOne(targetEntity="DeliveryMode")
	 * @ORM\JoinColumn(name="delivery_mode_id", referencedColumnName="id")
	 */
	private $deliveryModeId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="DeliveryTimeline")
	 * @ORM\JoinColumn(name="delivery_timeline_id", referencedColumnName="id")
	 */
	private $deliveryTimelineId;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return DeliveryModeTimeline
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
     * @return DeliveryModeTimeline
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
     * @return DeliveryModeTimeline
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
     * @return DeliveryModeTimeline
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
     * @return DeliveryModeTimeline
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
     * Set sourceZipCode
     *
     * @param string $sourceZipCode
     *
     * @return DeliveryModeTimeline
     */
    public function setSourceZipCode($sourceZipCode)
    {
        $this->sourceZipCode = $sourceZipCode;

        return $this;
    }

    /**
     * Get sourceZipCode
     *
     * @return string
     */
    public function getSourceZipCode()
    {
        return $this->sourceZipCode;
    }

    /**
     * Set destinationZipCode
     *
     * @param string $destinationZipCode
     *
     * @return DeliveryModeTimeline
     */
    public function setDestinationZipCode($destinationZipCode)
    {
        $this->destinationZipCode = $destinationZipCode;

        return $this;
    }

    /**
     * Get destinationZipCode
     *
     * @return string
     */
    public function getDestinationZipCode()
    {
        return $this->destinationZipCode;
    }

    /**
     * Set sourceCountryId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\Country $sourceCountryId
     *
     * @return DeliveryModeTimeline
     */
    public function setSourceCountryId(\NetFlex\DeliveryChargeBundle\Entity\Country $sourceCountryId = null)
    {
        $this->sourceCountryId = $sourceCountryId;

        return $this;
    }

    /**
     * Get sourceCountryId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\Country
     */
    public function getSourceCountryId()
    {
        return $this->sourceCountryId;
    }

    /**
     * Set sourceStateId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\State $sourceStateId
     *
     * @return DeliveryModeTimeline
     */
    public function setSourceStateId(\NetFlex\DeliveryChargeBundle\Entity\State $sourceStateId = null)
    {
        $this->sourceStateId = $sourceStateId;

        return $this;
    }

    /**
     * Get sourceStateId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\State
     */
    public function getSourceStateId()
    {
        return $this->sourceStateId;
    }

    /**
     * Set sourceCityId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\City $sourceCityId
     *
     * @return DeliveryModeTimeline
     */
    public function setSourceCityId(\NetFlex\DeliveryChargeBundle\Entity\City $sourceCityId = null)
    {
        $this->sourceCityId = $sourceCityId;

        return $this;
    }

    /**
     * Get sourceCityId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\City
     */
    public function getSourceCityId()
    {
        return $this->sourceCityId;
    }

    /**
     * Set destinationCountryId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\Country $destinationCountryId
     *
     * @return DeliveryModeTimeline
     */
    public function setDestinationCountryId(\NetFlex\DeliveryChargeBundle\Entity\Country $destinationCountryId = null)
    {
        $this->destinationCountryId = $destinationCountryId;

        return $this;
    }

    /**
     * Get destinationCountryId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\Country
     */
    public function getDestinationCountryId()
    {
        return $this->destinationCountryId;
    }

    /**
     * Set destinationStateId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\State $destinationStateId
     *
     * @return DeliveryModeTimeline
     */
    public function setDestinationStateId(\NetFlex\DeliveryChargeBundle\Entity\State $destinationStateId = null)
    {
        $this->destinationStateId = $destinationStateId;

        return $this;
    }

    /**
     * Get destinationStateId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\State
     */
    public function getDestinationStateId()
    {
        return $this->destinationStateId;
    }

    /**
     * Set destinationCityId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\City $destinationCityId
     *
     * @return DeliveryModeTimeline
     */
    public function setDestinationCityId(\NetFlex\DeliveryChargeBundle\Entity\City $destinationCityId = null)
    {
        $this->destinationCityId = $destinationCityId;

        return $this;
    }

    /**
     * Get destinationCityId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\City
     */
    public function getDestinationCityId()
    {
        return $this->destinationCityId;
    }

    /**
     * Set deliveryModeId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\DeliveryMode $deliveryModeId
     *
     * @return DeliveryModeTimeline
     */
    public function setDeliveryModeId(\NetFlex\DeliveryChargeBundle\Entity\DeliveryMode $deliveryModeId = null)
    {
        $this->deliveryModeId = $deliveryModeId;

        return $this;
    }

    /**
     * Get deliveryModeId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\DeliveryMode
     */
    public function getDeliveryModeId()
    {
        return $this->deliveryModeId;
    }

    /**
     * Set deliveryTimelineId
     *
     * @param \NetFlex\DeliveryChargeBundle\Entity\DeliveryTimeline $deliveryTimelineId
     *
     * @return DeliveryModeTimeline
     */
    public function setDeliveryTimelineId(\NetFlex\DeliveryChargeBundle\Entity\DeliveryTimeline $deliveryTimelineId = null)
    {
        $this->deliveryTimelineId = $deliveryTimelineId;

        return $this;
    }

    /**
     * Get deliveryTimelineId
     *
     * @return \NetFlex\DeliveryChargeBundle\Entity\DeliveryTimeline
     */
    public function getDeliveryTimelineId()
    {
        return $this->deliveryTimelineId;
    }
}
