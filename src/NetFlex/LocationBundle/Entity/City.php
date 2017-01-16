<?php

namespace NetFlex\LocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Entity\State;

/**
 * City
 *
 * @ORM\Table(name="cities")
 * @ORM\Entity(repositoryClass="NetFlex\LocationBundle\Repository\CityRepository")
 *
 * @UniqueEntity(
 *     fields={"countryId", "stateId", "name"},
 *     message="An identical city already exists",
 *     errorPath="name"
 * )
 */
class City
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
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="cities", cascade={"persist"})
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(
     *     message="Country is required"
     * )
	 */
	private $countryId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="State", inversedBy="cities", cascade={"persist"})
	 * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(
     *     message="State is required"
     * )
	 */
	private $stateId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Name is required"
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
	 * @return City
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
	 * @return City
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
     * Set name
     *
     * @param string $name
     *
     * @return City
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
     * @return City
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
     * @return City
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
     * @return City
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
     * @return City
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
     * @return City
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
