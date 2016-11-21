<?php

namespace NetFlex\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Entity\AddressType;

/**
 * Address
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity(repositoryClass="NetFlex\UserBundle\Repository\AddressRepository")
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
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="addresses", cascade={"persist"})
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $userId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="AddressType")
	 * @ORM\JoinColumn(name="address_type_id", referencedColumnName="id")
	 */
	private $addressTypeId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_primary", type="boolean")
     */
    private $isPrimary;

    /**
     * @var string
     *
     * @ORM\Column(name="address_line_1", type="string", length=255)
     */
    private $addressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="address_line_2", type="string", length=255)
     */
    private $addressLine2;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\Country")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $countryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\State")
	 * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
	 */
	private $stateId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\LocationBundle\Entity\City")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	private $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=255)
     */
    private $zipCode;

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
	 * @return Address
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
	 * Set addressTypeId
	 *
	 * @param AddressType $addressTypeId
	 *
	 * @return Address
	 */
	public function setAddressTypeId(AddressType $addressTypeId = null)
	{
		$this->addressTypeId = $addressTypeId;
		
		return $this;
	}
	
	/**
	 * Get addressTypeId
	 *
	 * @return AddressType
	 */
	public function getAddressTypeId()
	{
		return $this->addressTypeId;
	}

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return Address
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
     * Set addressLine1
     *
     * @param string $addressLine1
     *
     * @return Address
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    /**
     * Get addressLine1
     *
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set addressLine2
     *
     * @param string $addressLine2
     *
     * @return Address
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    /**
     * Get addressLine2
     *
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }
	
	/**
	 * Set countryId
	 *
	 * @param Country $countryId
	 *
	 * @return Address
	 */
	public function setCountryId(Country $countryId = null)
	{
		$this->countryId = $countryId;
		
		return $this;
	}
	
	/**
	 * Get countryId
	 *
	 * @return Country
	 */
	public function getCountryId()
	{
		return $this->countryId;
	}
	
	/**
	 * Set stateId
	 *
	 * @param State $stateId
	 *
	 * @return Address
	 */
	public function setStateId(State $stateId = null)
	{
		$this->stateId = $stateId;
		
		return $this;
	}
	
	/**
	 * Get stateId
	 *
	 * @return State
	 */
	public function getStateId()
	{
		return $this->stateId;
	}
	
	/**
	 * Set cityId
	 *
	 * @param City $cityId
	 *
	 * @return Address
	 */
	public function setCityId(City $cityId = null)
	{
		$this->cityId = $cityId;
		
		return $this;
	}
	
	/**
	 * Get cityId
	 *
	 * @return City
	 */
	public function getCityId()
	{
		return $this->cityId;
	}

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Address
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
