<?php

namespace NetFlex\DeliveryChargeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Entity\State;
use NetFlex\LocationBundle\Entity\City;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\DeliveryChargeBundle\Entity\WeightUnit;
use NetFlex\DeliveryChargeBundle\Entity\Currency;

/**
 * DeliveryCharge
 *
 * @ORM\Table(name="delivery_charges")
 * @ORM\Entity(repositoryClass="NetFlex\DeliveryChargeBundle\Repository\DeliveryChargeRepository")
 */
class DeliveryCharge
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
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="source_country_id", referencedColumnName="id", nullable=true)
	 */
	private $sourceCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="source_state_id", referencedColumnName="id", nullable=true)
	 */
	private $sourceStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
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
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="destination_country_id", referencedColumnName="id", nullable=true)
	 */
	private $destinationCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="destination_state_id", referencedColumnName="id", nullable=true)
	 */
	private $destinationStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
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
	 * @ORM\OneToOne(targetEntity="DeliveryModeTimeline")
	 * @ORM\JoinColumn(name="delivery_mode_timeline_id", referencedColumnName="id")
	 */
	private $deliveryModeTimelineId;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_base_weight_upper_limit", type="decimal", precision=10, scale=2)
     */
    private $shipmentBaseWeightUpperLimit;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_base_weight_lower_limit", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $shipmentBaseWeightLowerLimit;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_accountable_extra_weight", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $shipmentAccountableExtraWeight;
	
	/**
	 * @ORM\ManyToOne(targetEntity="WeightUnit")
	 * @ORM\JoinColumn(name="shipment_weight_unit_id", referencedColumnName="id")
	 */
	private $shipmentWeightUnitId;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $deliveryBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_extra_price_multiplier", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $deliveryExtraPriceMultiplier;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_delivery_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $codDeliveryBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_delivery_percentage_on_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $codDeliveryPercentageOnBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="fuel_surcharge_fixed_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $fuelSurchargeFixedPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="fuel_surcharge_percentage_on_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $fuelSurchargePercentageOnBasePrice;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="service_tax_percentage_on_base_price", type="decimal", precision=10, scale=2, nullable=true)
	 */
	private $serviceTaxPercentageOnBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_risk_type", type="string", length=255, nullable=true)
     */
    private $shipmentRiskType;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_risk_chargable_above", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $shipmentRiskChargableAbove;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_risk_percentage_on_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $shipmentRiskPercentageOnBasePrice;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Currency")
	 * @ORM\JoinColumn(name="delivery_price_unit_id", referencedColumnName="id")
	 */
	private $deliveryPriceUnitId;

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
	 * Set sourceCountryId
	 *
	 * @param Country $sourceCountryId
	 *
	 * @return DeliveryCharge
	 */
	public function setSourceCountryId(Country $sourceCountryId = null)
	{
		$this->sourceCountryId = $sourceCountryId;
		
		return $this;
	}
	
	/**
	 * Get sourceCountryId
	 *
	 * @return Country
	 */
	public function getSourceCountryId()
	{
		return $this->sourceCountryId;
	}
	
	/**
	 * Set sourceStateId
	 *
	 * @param State $sourceStateId
	 *
	 * @return DeliveryCharge
	 */
	public function setSourceStateId(State $sourceStateId = null)
	{
		$this->sourceStateId = $sourceStateId;
		
		return $this;
	}
	
	/**
	 * Get sourceStateId
	 *
	 * @return State
	 */
	public function getSourceStateId()
	{
		return $this->sourceStateId;
	}
	
	/**
	 * Set sourceCityId
	 *
	 * @param City $sourceCityId
	 *
	 * @return DeliveryCharge
	 */
	public function setSourceCityId(City $sourceCityId = null)
	{
		$this->sourceCityId = $sourceCityId;
		
		return $this;
	}
	
	/**
	 * Get sourceCityId
	 *
	 * @return City
	 */
	public function getSourceCityId()
	{
		return $this->sourceCityId;
	}

    /**
     * Set sourceZipCode
     *
     * @param string $sourceZipCode
     *
     * @return DeliveryCharge
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
	 * Set destinationCountryId
	 *
	 * @param Country $destinationCountryId
	 *
	 * @return DeliveryCharge
	 */
	public function setDestinationCountryId(Country $destinationCountryId = null)
	{
		$this->destinationCountryId = $destinationCountryId;
		
		return $this;
	}
	
	/**
	 * Get destinationCountryId
	 *
	 * @return Country
	 */
	public function getDestinationCountryId()
	{
		return $this->destinationCountryId;
	}
	
	/**
	 * Set destinationStateId
	 *
	 * @param State $destinationStateId
	 *
	 * @return DeliveryCharge
	 */
	public function setDestinationStateId(State $destinationStateId = null)
	{
		$this->destinationStateId = $destinationStateId;
		
		return $this;
	}
	
	/**
	 * Get destinationStateId
	 *
	 * @return State
	 */
	public function getDestinationStateId()
	{
		return $this->destinationStateId;
	}
	
	/**
	 * Set destinationCityId
	 *
	 * @param City $destinationCityId
	 *
	 * @return DeliveryCharge
	 */
	public function setDestinationCityId(City $destinationCityId = null)
	{
		$this->destinationCityId = $destinationCityId;
		
		return $this;
	}
	
	/**
	 * Get destinationCityId
	 *
	 * @return City
	 */
	public function getDestinationCityId()
	{
		return $this->destinationCityId;
	}

    /**
     * Set destinationZipCode
     *
     * @param string $destinationZipCode
     *
     * @return DeliveryCharge
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
	 * Set deliveryModeTimelineId
	 *
	 * @param DeliveryModeTimeline $deliveryModeTimelineId
	 *
	 * @return DeliveryCharge
	 */
	public function setDeliveryModeTimelineId(DeliveryModeTimeline $deliveryModeTimelineId = null)
	{
		$this->deliveryModeTimelineId = $deliveryModeTimelineId;
		
		return $this;
	}
	
	/**
	 * Get deliveryModeTimelineId
	 *
	 * @return DeliveryModeTimeline
	 */
	public function getDeliveryModeTimelineId()
	{
		return $this->deliveryModeTimelineId;
	}

    /**
     * Set shipmentBaseWeightUpperLimit
     *
     * @param string $shipmentBaseWeightUpperLimit
     *
     * @return DeliveryCharge
     */
    public function setShipmentBaseWeightUpperLimit($shipmentBaseWeightUpperLimit)
    {
        $this->shipmentBaseWeightUpperLimit = $shipmentBaseWeightUpperLimit;

        return $this;
    }

    /**
     * Get shipmentBaseWeightUpperLimit
     *
     * @return string
     */
    public function getShipmentBaseWeightUpperLimit()
    {
        return $this->shipmentBaseWeightUpperLimit;
    }

    /**
     * Set shipmentBaseWeightLowerLimit
     *
     * @param string $shipmentBaseWeightLowerLimit
     *
     * @return DeliveryCharge
     */
    public function setShipmentBaseWeightLowerLimit($shipmentBaseWeightLowerLimit)
    {
        $this->shipmentBaseWeightLowerLimit = $shipmentBaseWeightLowerLimit;

        return $this;
    }

    /**
     * Get shipmentBaseWeightLowerLimit
     *
     * @return string
     */
    public function getShipmentBaseWeightLowerLimit()
    {
        return $this->shipmentBaseWeightLowerLimit;
    }

    /**
     * Set shipmentAccountableExtraWeight
     *
     * @param string $shipmentAccountableExtraWeight
     *
     * @return DeliveryCharge
     */
    public function setShipmentAccountableExtraWeight($shipmentAccountableExtraWeight)
    {
        $this->shipmentAccountableExtraWeight = $shipmentAccountableExtraWeight;

        return $this;
    }

    /**
     * Get shipmentAccountableExtraWeight
     *
     * @return string
     */
    public function getShipmentAccountableExtraWeight()
    {
        return $this->shipmentAccountableExtraWeight;
    }
	
	/**
	 * Set shipmentWeightUnitId
	 *
	 * @param WeightUnit $shipmentWeightUnitId
	 *
	 * @return DeliveryCharge
	 */
	public function setShipmentWeightUnitId(WeightUnit $shipmentWeightUnitId = null)
	{
		$this->shipmentWeightUnitId = $shipmentWeightUnitId;
		
		return $this;
	}
	
	/**
	 * Get shipmentWeightUnitId
	 *
	 * @return WeightUnit
	 */
	public function getShipmentWeightUnitId()
	{
		return $this->shipmentWeightUnitId;
	}

    /**
     * Set deliveryBasePrice
     *
     * @param string $deliveryBasePrice
     *
     * @return DeliveryCharge
     */
    public function setDeliveryBasePrice($deliveryBasePrice)
    {
        $this->deliveryBasePrice = $deliveryBasePrice;

        return $this;
    }

    /**
     * Get deliveryBasePrice
     *
     * @return string
     */
    public function getDeliveryBasePrice()
    {
        return $this->deliveryBasePrice;
    }

    /**
     * Set deliveryExtraPriceMultiplier
     *
     * @param string $deliveryExtraPriceMultiplier
     *
     * @return DeliveryCharge
     */
    public function setDeliveryExtraPriceMultiplier($deliveryExtraPriceMultiplier)
    {
        $this->deliveryExtraPriceMultiplier = $deliveryExtraPriceMultiplier;

        return $this;
    }

    /**
     * Get deliveryExtraPriceMultiplier
     *
     * @return string
     */
    public function getDeliveryExtraPriceMultiplier()
    {
        return $this->deliveryExtraPriceMultiplier;
    }

    /**
     * Set codDeliveryBasePrice
     *
     * @param string $codDeliveryBasePrice
     *
     * @return DeliveryCharge
     */
    public function setCodDeliveryBasePrice($codDeliveryBasePrice)
    {
        $this->codDeliveryBasePrice = $codDeliveryBasePrice;

        return $this;
    }

    /**
     * Get codDeliveryBasePrice
     *
     * @return string
     */
    public function getCodDeliveryBasePrice()
    {
        return $this->codDeliveryBasePrice;
    }

    /**
     * Set codDeliveryPercentageOnBasePrice
     *
     * @param string $codDeliveryPercentageOnBasePrice
     *
     * @return DeliveryCharge
     */
    public function setCodDeliveryPercentageOnBasePrice($codDeliveryPercentageOnBasePrice)
    {
        $this->codDeliveryPercentageOnBasePrice = $codDeliveryPercentageOnBasePrice;

        return $this;
    }

    /**
     * Get codDeliveryPercentageOnBasePrice
     *
     * @return string
     */
    public function getCodDeliveryPercentageOnBasePrice()
    {
        return $this->codDeliveryPercentageOnBasePrice;
    }

    /**
     * Set fuelSurchargeFixedPrice
     *
     * @param string $fuelSurchargeFixedPrice
     *
     * @return DeliveryCharge
     */
    public function setFuelSurchargeFixedPrice($fuelSurchargeFixedPrice)
    {
        $this->fuelSurchargeFixedPrice = $fuelSurchargeFixedPrice;

        return $this;
    }

    /**
     * Get fuelSurchargeFixedPrice
     *
     * @return string
     */
    public function getFuelSurchargeFixedPrice()
    {
        return $this->fuelSurchargeFixedPrice;
    }

    /**
     * Set fuelSurchargePercentageOnBasePrice
     *
     * @param string $fuelSurchargePercentageOnBasePrice
     *
     * @return DeliveryCharge
     */
    public function setFuelSurchargePercentageOnBasePrice($fuelSurchargePercentageOnBasePrice)
    {
        $this->fuelSurchargePercentageOnBasePrice = $fuelSurchargePercentageOnBasePrice;

        return $this;
    }

    /**
     * Get fuelSurchargePercentageOnBasePrice
     *
     * @return string
     */
    public function getFuelSurchargePercentageOnBasePrice()
    {
        return $this->fuelSurchargePercentageOnBasePrice;
    }
	
	/**
	 * Set serviceTaxPercentageOnBasePrice
	 *
	 * @param string $serviceTaxPercentageOnBasePrice
	 *
	 * @return DeliveryCharge
	 */
	public function setServiceTaxPercentageOnBasePrice($serviceTaxPercentageOnBasePrice)
	{
		$this->serviceTaxPercentageOnBasePrice = $serviceTaxPercentageOnBasePrice;
		
		return $this;
	}
	
	/**
	 * Get serviceTaxPercentageOnBasePrice
	 *
	 * @return string
	 */
	public function getServiceTaxPercentageOnBasePrice()
	{
		return $this->serviceTaxPercentageOnBasePrice;
	}

    /**
     * Set shipmentRiskType
     *
     * @param string $shipmentRiskType
     *
     * @return DeliveryCharge
     */
    public function setShipmentRiskType($shipmentRiskType)
    {
        $this->shipmentRiskType = $shipmentRiskType;

        return $this;
    }

    /**
     * Get shipmentRiskType
     *
     * @return string
     */
    public function getShipmentRiskType()
    {
        return $this->shipmentRiskType;
    }

    /**
     * Set shipmentRiskChargableAbove
     *
     * @param string $shipmentRiskChargableAbove
     *
     * @return DeliveryCharge
     */
    public function setShipmentRiskChargableAbove($shipmentRiskChargableAbove)
    {
        $this->shipmentRiskChargableAbove = $shipmentRiskChargableAbove;

        return $this;
    }

    /**
     * Get shipmentRiskChargableAbove
     *
     * @return string
     */
    public function getShipmentRiskChargableAbove()
    {
        return $this->shipmentRiskChargableAbove;
    }

    /**
     * Set shipmentRiskPercentageOnBasePrice
     *
     * @param string $shipmentRiskPercentageOnBasePrice
     *
     * @return DeliveryCharge
     */
    public function setShipmentRiskPercentageOnBasePrice($shipmentRiskPercentageOnBasePrice)
    {
        $this->shipmentRiskPercentageOnBasePrice = $shipmentRiskPercentageOnBasePrice;

        return $this;
    }

    /**
     * Get shipmentRiskPercentageOnBasePrice
     *
     * @return string
     */
    public function getShipmentRiskPercentageOnBasePrice()
    {
        return $this->shipmentRiskPercentageOnBasePrice;
    }
	
	/**
	 * Set deliveryPriceUnitId
	 *
	 * @param Currency $deliveryPriceUnitId
	 *
	 * @return DeliveryCharge
	 */
	public function setDeliveryPriceUnitId(Currency $deliveryPriceUnitId = null)
	{
		$this->deliveryPriceUnitId = $deliveryPriceUnitId;
		
		return $this;
	}
	
	/**
	 * Get deliveryPriceUnitId
	 *
	 * @return Currency
	 */
	public function getDeliveryPriceUnitId()
	{
		return $this->deliveryPriceUnitId;
	}

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return DeliveryCharge
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
     * @return DeliveryCharge
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
     * @return DeliveryCharge
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
     * @return DeliveryCharge
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
     * @return DeliveryCharge
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
	    return '';
    }
}
