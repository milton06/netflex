<?php

namespace NetFlex\LocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NetFlex\LocationBundle\Entity\State;
use NetFlex\LocationBundle\Entity\City;

/**
 * Country
 *
 * @ORM\Table(name="countries")
 * @ORM\Entity(repositoryClass="NetFlex\LocationBundle\Repository\CountryRepository")
 *
 * @UniqueEntity(
 *     fields={"code"},
 *     message="A country with the same code already exists"
 * ) *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="A country with the same name already exists"
 * )
 */
class Country
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
	 * @ORM\OneToMany(targetEntity="State", mappedBy="countryId")
	 */
	private $states;
	
	/**
	 * @ORM\OneToMany(targetEntity="City", mappedBy="countryId")
	 */
	private $cities;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Country code is required"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=3,
     *     minMessage="Country code must be of atleast 2 characters",
     *     maxMessage="Country code cannot exceed 3 characters"
     * )
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Country name is required"
     * )
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
		$this->states = new ArrayCollection();
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
	 * Add state
	 *
	 * @param State $state
	 *
	 * @return Country
	 */
	public function addState(State $state)
	{
		$state->setCountryId($this);
		
		$this->states[] = $state;
		
		return $this;
	}
	
	/**
	 * Remove state
	 *
	 * @param State $state
	 */
	public function removeState(State $state)
	{
		$state->setCountryId(null);
		
		$this->states->removeElement($state);
	}
	
	/**
	 * Get states
	 *
	 * @return ArrayCollection
	 */
	public function getStates()
	{
		return $this->states;
	}
	
	/**
	 * Add city
	 *
	 * @param City $city
	 *
	 * @return Country
	 */
	public function addCity(City $city)
	{
		$city->setCountryId($this);
		
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
     * Set code
     *
     * @param string $code
     *
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Country
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
     * @return Country
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
     * @return Country
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
     * @return Country
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
     * @return Country
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
     * @return Country
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
