<?php

namespace NetFlex\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Entity\State;
use NetFlex\LocationBundle\Entity\City;
use NetFlex\OrderBundle\Entity\OrderTransaction;

/**
 * Address
 *
 * @ORM\Table(name="order_addresses")
 * @ORM\Entity(repositoryClass="NetFlex\OrderBundle\Repository\AddressRepository")
 */
class Address
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
	 * @ORM\OneToOne(targetEntity="OrderTransaction", inversedBy="orderAddress")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_first_name", type="string", length=255)
     */
    private $pickupFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_mid_name", type="string", length=255, nullable=true)
     */
    private $pickupMidName;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_last_name", type="string", length=255)
     */
    private $pickupLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_address_line_1", type="string", length=255)
     */
    private $pickupAddressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_address_line_2", type="string", length=255, nullable=true)
     */
    private $pickupAddressLine2;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="pickup_country_id", referencedColumnName="id")
	 */
	private $pickupCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="pickup_state_id", referencedColumnName="id")
	 */
	private $pickupStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
	 * @ORM\JoinColumn(name="pickup_city_id", referencedColumnName="id")
	 */
	private $pickupCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_zip_code", type="string", length=255)
     */
    private $pickupZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_land_mark", type="string", length=255, nullable=true)
     */
    private $pickupLandMark;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_email", type="string", length=255, nullable=true)
     */
    private $pickupEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="pickup_contact_number", type="string", length=255)
     */
    private $pickupContactNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_first_name", type="string", length=255)
     */
    private $billingFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_mid_name", type="string", length=255, nullable=true)
     */
    private $billingMidName;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_last_name", type="string", length=255)
     */
    private $billingLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_address_line_1", type="string", length=255)
     */
    private $billingAddressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_address_line_2", type="string", length=255, nullable=true)
     */
    private $billingAddressLine2;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="billing_country_id", referencedColumnName="id")
	 */
	private $billingCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="billing_state_id", referencedColumnName="id")
	 */
	private $billingStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
	 * @ORM\JoinColumn(name="billing_city_id", referencedColumnName="id")
	 */
	private $billingCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_zip_code", type="string", length=255)
     */
    private $billingZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_land_mark", type="string", length=255, nullable=true)
     */
    private $billingLandMark;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_email", type="string", length=255)
     */
    private $billingEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_contact_number", type="string", length=255)
     */
    private $billingContactNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_first_name", type="string", length=255)
     */
    private $shippingFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_mid_name", type="string", length=255, nullable=true)
     */
    private $shippingMidName;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_last_name", type="string", length=255)
     */
    private $shippingLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address_line_1", type="string", length=255)
     */
    private $shippingAddressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address_line_2", type="string", length=255, nullable=true)
     */
    private $shippingAddressLine2;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="shipping_country_id", referencedColumnName="id")
	 */
	private $shippingCountryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="shipping_state_id", referencedColumnName="id")
	 */
	private $shippingStateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
	 * @ORM\JoinColumn(name="shipping_city_id", referencedColumnName="id")
	 */
	private $shippingCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_zip_code", type="string", length=255)
     */
    private $shippingZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_land_mark", type="string", length=255, nullable=true)
     */
    private $shippingLandMark;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_email", type="string", length=255, nullable=true)
     */
    private $shippingEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_contact_number", type="string", length=255)
     */
    private $shippingContactNumber;


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
	 * @return Address
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
     * Set pickupFirstName
     *
     * @param string $pickupFirstName
     *
     * @return Address
     */
    public function setPickupFirstName($pickupFirstName)
    {
        $this->pickupFirstName = $pickupFirstName;

        return $this;
    }

    /**
     * Get pickupFirstName
     *
     * @return string
     */
    public function getPickupFirstName()
    {
        return $this->pickupFirstName;
    }

    /**
     * Set pickupMidName
     *
     * @param string $pickupMidName
     *
     * @return Address
     */
    public function setPickupMidName($pickupMidName)
    {
        $this->pickupMidName = $pickupMidName;

        return $this;
    }

    /**
     * Get pickupMidName
     *
     * @return string
     */
    public function getPickupMidName()
    {
        return $this->pickupMidName;
    }

    /**
     * Set pickupLastName
     *
     * @param string $pickupLastName
     *
     * @return Address
     */
    public function setPickupLastName($pickupLastName)
    {
        $this->pickupLastName = $pickupLastName;

        return $this;
    }

    /**
     * Get pickupLastName
     *
     * @return string
     */
    public function getPickupLastName()
    {
        return $this->pickupLastName;
    }

    /**
     * Set pickupAddressLine1
     *
     * @param string $pickupAddressLine1
     *
     * @return Address
     */
    public function setPickupAddressLine1($pickupAddressLine1)
    {
        $this->pickupAddressLine1 = $pickupAddressLine1;

        return $this;
    }

    /**
     * Get pickupAddressLine1
     *
     * @return string
     */
    public function getPickupAddressLine1()
    {
        return $this->pickupAddressLine1;
    }

    /**
     * Set pickupAddressLine2
     *
     * @param string $pickupAddressLine2
     *
     * @return Address
     */
    public function setPickupAddressLine2($pickupAddressLine2)
    {
        $this->pickupAddressLine2 = $pickupAddressLine2;

        return $this;
    }

    /**
     * Get pickupAddressLine2
     *
     * @return string
     */
    public function getPickupAddressLine2()
    {
        return $this->pickupAddressLine2;
    }
	
	/**
	 * Set pickupCountryId
	 *
	 * @param Country $pickupCountryId
	 *
	 * @return Address
	 */
	public function setPickupCountryId(Country $pickupCountryId = null)
	{
		$this->pickupCountryId = $pickupCountryId;
		
		return $this;
	}
	
	/**
	 * Get pickupCountryId
	 *
	 * @return Country
	 */
	public function getPickupCountryId()
	{
		return $this->pickupCountryId;
	}
	
	/**
	 * Set pickupStateId
	 *
	 * @param State $pickupStateId
	 *
	 * @return Address
	 */
	public function setPickupStateId(State $pickupStateId = null)
	{
		$this->pickupStateId = $pickupStateId;
		
		return $this;
	}
	
	/**
	 * Get pickupStateId
	 *
	 * @return State
	 */
	public function getPickupStateId()
	{
		return $this->pickupStateId;
	}
	
	/**
	 * Set pickupCityId
	 *
	 * @param City $pickupCityId
	 *
	 * @return Address
	 */
	public function setPickupCityId(City $pickupCityId = null)
	{
		$this->pickupCityId = $pickupCityId;
		
		return $this;
	}
	
	/**
	 * Get pickupCityId
	 *
	 * @return City
	 */
	public function getPickupCityId()
	{
		return $this->pickupCityId;
	}

    /**
     * Set pickupZipCode
     *
     * @param string $pickupZipCode
     *
     * @return Address
     */
    public function setPickupZipCode($pickupZipCode)
    {
        $this->pickupZipCode = $pickupZipCode;

        return $this;
    }

    /**
     * Get pickupZipCode
     *
     * @return string
     */
    public function getPickupZipCode()
    {
        return $this->pickupZipCode;
    }

    /**
     * Set pickupLandMark
     *
     * @param string $pickupLandMark
     *
     * @return Address
     */
    public function setPickupLandMark($pickupLandMark)
    {
        $this->pickupLandMark = $pickupLandMark;

        return $this;
    }

    /**
     * Get pickupLandMark
     *
     * @return string
     */
    public function getPickupLandMark()
    {
        return $this->pickupLandMark;
    }

    /**
     * Set pickupEmail
     *
     * @param string $pickupEmail
     *
     * @return Address
     */
    public function setPickupEmail($pickupEmail)
    {
        $this->pickupEmail = $pickupEmail;

        return $this;
    }

    /**
     * Get pickupEmail
     *
     * @return string
     */
    public function getPickupEmail()
    {
        return $this->pickupEmail;
    }

    /**
     * Set pickupContactNumber
     *
     * @param string $pickupContactNumber
     *
     * @return Address
     */
    public function setPickupContactNumber($pickupContactNumber)
    {
        $this->pickupContactNumber = $pickupContactNumber;

        return $this;
    }

    /**
     * Get pickupContactNumber
     *
     * @return string
     */
    public function getPickupContactNumber()
    {
        return $this->pickupContactNumber;
    }

    /**
     * Set billingFirstName
     *
     * @param string $billingFirstName
     *
     * @return Address
     */
    public function setBillingFirstName($billingFirstName)
    {
        $this->billingFirstName = $billingFirstName;

        return $this;
    }

    /**
     * Get billingFirstName
     *
     * @return string
     */
    public function getBillingFirstName()
    {
        return $this->billingFirstName;
    }

    /**
     * Set billingMidName
     *
     * @param string $billingMidName
     *
     * @return Address
     */
    public function setBillingMidName($billingMidName)
    {
        $this->billingMidName = $billingMidName;

        return $this;
    }

    /**
     * Get billingMidName
     *
     * @return string
     */
    public function getBillingMidName()
    {
        return $this->billingMidName;
    }

    /**
     * Set billingLastName
     *
     * @param string $billingLastName
     *
     * @return Address
     */
    public function setBillingLastName($billingLastName)
    {
        $this->billingLastName = $billingLastName;

        return $this;
    }

    /**
     * Get billingLastName
     *
     * @return string
     */
    public function getBillingLastName()
    {
        return $this->billingLastName;
    }

    /**
     * Set billingAddressLine1
     *
     * @param string $billingAddressLine1
     *
     * @return Address
     */
    public function setBillingAddressLine1($billingAddressLine1)
    {
        $this->billingAddressLine1 = $billingAddressLine1;

        return $this;
    }

    /**
     * Get billingAddressLine1
     *
     * @return string
     */
    public function getBillingAddressLine1()
    {
        return $this->billingAddressLine1;
    }

    /**
     * Set billingAddressLine2
     *
     * @param string $billingAddressLine2
     *
     * @return Address
     */
    public function setBillingAddressLine2($billingAddressLine2)
    {
        $this->billingAddressLine2 = $billingAddressLine2;

        return $this;
    }

    /**
     * Get billingAddressLine2
     *
     * @return string
     */
    public function getBillingAddressLine2()
    {
        return $this->billingAddressLine2;
    }
	
	/**
	 * Set billingCountryId
	 *
	 * @param Country $billingCountryId
	 *
	 * @return Address
	 */
	public function setBillingCountryId(Country $billingCountryId = null)
	{
		$this->billingCountryId = $billingCountryId;
		
		return $this;
	}
	
	/**
	 * Get billingCountryId
	 *
	 * @return Country
	 */
	public function getBillingCountryId()
	{
		return $this->billingCountryId;
	}
	
	/**
	 * Set billingStateId
	 *
	 * @param State $billingStateId
	 *
	 * @return Address
	 */
	public function setBillingStateId(State $billingStateId = null)
	{
		$this->billingStateId = $billingStateId;
		
		return $this;
	}
	
	/**
	 * Get billingStateId
	 *
	 * @return State
	 */
	public function getBillingStateId()
	{
		return $this->billingStateId;
	}
	
	/**
	 * Set billingCityId
	 *
	 * @param City $billingCityId
	 *
	 * @return Address
	 */
	public function setBillingCityId(City $billingCityId = null)
	{
		$this->billingCityId = $billingCityId;
		
		return $this;
	}
	
	/**
	 * Get billingCityId
	 *
	 * @return City
	 */
	public function getBillingCityId()
	{
		return $this->billingCityId;
	}

    /**
     * Set billingZipCode
     *
     * @param string $billingZipCode
     *
     * @return Address
     */
    public function setBillingZipCode($billingZipCode)
    {
        $this->billingZipCode = $billingZipCode;

        return $this;
    }

    /**
     * Get billingZipCode
     *
     * @return string
     */
    public function getBillingZipCode()
    {
        return $this->billingZipCode;
    }

    /**
     * Set billingLandMark
     *
     * @param string $billingLandMark
     *
     * @return Address
     */
    public function setBillingLandMark($billingLandMark)
    {
        $this->billingLandMark = $billingLandMark;

        return $this;
    }

    /**
     * Get billingLandMark
     *
     * @return string
     */
    public function getBillingLandMark()
    {
        return $this->billingLandMark;
    }

    /**
     * Set billingEmail
     *
     * @param string $billingEmail
     *
     * @return Address
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;

        return $this;
    }

    /**
     * Get billingEmail
     *
     * @return string
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * Set billingContactNumber
     *
     * @param string $billingContactNumber
     *
     * @return Address
     */
    public function setBillingContactNumber($billingContactNumber)
    {
        $this->billingContactNumber = $billingContactNumber;

        return $this;
    }

    /**
     * Get billingContactNumber
     *
     * @return string
     */
    public function getBillingContactNumber()
    {
        return $this->billingContactNumber;
    }

    /**
     * Set shippingFirstName
     *
     * @param string $shippingFirstName
     *
     * @return Address
     */
    public function setShippingFirstName($shippingFirstName)
    {
        $this->shippingFirstName = $shippingFirstName;

        return $this;
    }

    /**
     * Get shippingFirstName
     *
     * @return string
     */
    public function getShippingFirstName()
    {
        return $this->shippingFirstName;
    }

    /**
     * Set shippingMidName
     *
     * @param string $shippingMidName
     *
     * @return Address
     */
    public function setShippingMidName($shippingMidName)
    {
        $this->shippingMidName = $shippingMidName;

        return $this;
    }

    /**
     * Get shippingMidName
     *
     * @return string
     */
    public function getShippingMidName()
    {
        return $this->shippingMidName;
    }

    /**
     * Set shippingLastName
     *
     * @param string $shippingLastName
     *
     * @return Address
     */
    public function setShippingLastName($shippingLastName)
    {
        $this->shippingLastName = $shippingLastName;

        return $this;
    }

    /**
     * Get shippingLastName
     *
     * @return string
     */
    public function getShippingLastName()
    {
        return $this->shippingLastName;
    }

    /**
     * Set shippingAddressLine1
     *
     * @param string $shippingAddressLine1
     *
     * @return Address
     */
    public function setShippingAddressLine1($shippingAddressLine1)
    {
        $this->shippingAddressLine1 = $shippingAddressLine1;

        return $this;
    }

    /**
     * Get shippingAddressLine1
     *
     * @return string
     */
    public function getShippingAddressLine1()
    {
        return $this->shippingAddressLine1;
    }

    /**
     * Set shippingAddressLine2
     *
     * @param string $shippingAddressLine2
     *
     * @return Address
     */
    public function setShippingAddressLine2($shippingAddressLine2)
    {
        $this->shippingAddressLine2 = $shippingAddressLine2;

        return $this;
    }

    /**
     * Get shippingAddressLine2
     *
     * @return string
     */
    public function getShippingAddressLine2()
    {
        return $this->shippingAddressLine2;
    }
	
	/**
	 * Set shippingCountryId
	 *
	 * @param Country $shippingCountryId
	 *
	 * @return Address
	 */
	public function setShippingCountryId(Country $shippingCountryId = null)
	{
		$this->shippingCountryId = $shippingCountryId;
		
		return $this;
	}
	
	/**
	 * Get shippingCountryId
	 *
	 * @return Country
	 */
	public function getShippingCountryId()
	{
		return $this->shippingCountryId;
	}
	
	/**
	 * Set shippingStateId
	 *
	 * @param State $shippingStateId
	 *
	 * @return Address
	 */
	public function setShippingStateId(State $shippingStateId = null)
	{
		$this->shippingStateId = $shippingStateId;
		
		return $this;
	}
	
	/**
	 * Get shippingStateId
	 *
	 * @return State
	 */
	public function getShippingStateId()
	{
		return $this->shippingStateId;
	}
	
	/**
	 * Set shippingCityId
	 *
	 * @param City $shippingCityId
	 *
	 * @return Address
	 */
	public function setShippingCityId(City $shippingCityId = null)
	{
		$this->shippingCityId = $shippingCityId;
		
		return $this;
	}
	
	/**
	 * Get shippingCityId
	 *
	 * @return City
	 */
	public function getShippingCityId()
	{
		return $this->shippingCityId;
	}
	
	/**
	 * Set shippingZipCode
	 *
	 * @param string $shippingZipCode
	 *
	 * @return Address
	 */
	public function setShippingZipCode($shippingZipCode)
	{
		$this->shippingZipCode = $shippingZipCode;
		
		return $this;
	}

    /**
     * Get shippingZipCode
     *
     * @return string
     */
    public function getShippingZipCode()
    {
        return $this->shippingZipCode;
    }

    /**
     * Set shippingLandMark
     *
     * @param string $shippingLandMark
     *
     * @return Address
     */
    public function setShippingLandMark($shippingLandMark)
    {
        $this->shippingLandMark = $shippingLandMark;

        return $this;
    }

    /**
     * Get shippingLandMark
     *
     * @return string
     */
    public function getShippingLandMark()
    {
        return $this->shippingLandMark;
    }

    /**
     * Set shippingEmail
     *
     * @param string $shippingEmail
     *
     * @return Address
     */
    public function setShippingEmail($shippingEmail)
    {
        $this->shippingEmail = $shippingEmail;

        return $this;
    }

    /**
     * Get shippingEmail
     *
     * @return string
     */
    public function getShippingEmail()
    {
        return $this->shippingEmail;
    }

    /**
     * Set shippingContactNumber
     *
     * @param string $shippingContactNumber
     *
     * @return Address
     */
    public function setShippingContactNumber($shippingContactNumber)
    {
        $this->shippingContactNumber = $shippingContactNumber;

        return $this;
    }

    /**
     * Get shippingContactNumber
     *
     * @return string
     */
    public function getShippingContactNumber()
    {
        return $this->shippingContactNumber;
    }
}
