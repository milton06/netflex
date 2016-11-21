<?php

namespace NetFlex\LocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Entity\City;

/**
 * State
 *
 * @ORM\Table(name="states")
 * @ORM\Entity(repositoryClass="NetFlex\LocationBundle\Repository\StateRepository")
 */
class State
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
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="states", cascade={"persist"})
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $countryId;
	
	/**
	 * @ORM\OneToMany(targetEntity="City", mappedBy="stateId")
	 */
	private $cities;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
		$this->cities = new ArrayCollection();
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
	 * Set countryId
	 *
	 * @param Country $countryId
	 *
	 * @return State
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
	 * Add city
	 *
	 * @param City $city
	 *
	 * @return State
	 */
	public function addCity(City $city)
	{
		$city->setStateId($this);
		
		$this->cities[] = $city;
		
		return $this;
	}
	
	/**
	 * Remove city
	 *
	 * @param City $city
	 */
	public function removeCity(City $city)
	{
		$city->setStateId(null);
		
		$this->cities->removeElement($city);
	}
	
	/**
	 * Get cities
	 *
	 * @return ArrayCollection
	 */
	public function getCities()
	{
		return $this->cities;
	}

    /**
     * Set name
     *
     * @param string $name
     *
     * @return State
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
     * Set status
     *
     * @param boolean $status
     *
     * @return State
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
     * Set cratedOn
     *
     * @param \DateTime $cratedOn
     *
     * @return State
     */
    public function setCratedOn($cratedOn)
    {
        $this->cratedOn = $cratedOn;

        return $this;
    }

    /**
     * Get cratedOn
     *
     * @return \DateTime
     */
    public function getCratedOn()
    {
        return $this->cratedOn;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return State
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
     * @return State
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
     * @return State
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
		return $this->name;
	}
}
