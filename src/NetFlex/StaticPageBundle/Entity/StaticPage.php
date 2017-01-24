<?php

namespace NetFlex\StaticPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NetFlex\StaticPageBundle\Entity\StaticPageSection;

/**
 * StaticPage
 *
 * @ORM\Table(name="static_pages")
 * @ORM\Entity(repositoryClass="NetFlex\StaticPageBundle\Repository\StaticPageRepository")
 *
 * @UniqueEntity(
 *     fields={"title"},
 *     message="A static page with the same title already exists"
 * )
 * @UniqueEntity(
 *     fields={"slug"},
 *     message="A static page with the same slug already exists"
 * )
 */
class StaticPage
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
     * @ORM\OneToMany(targetEntity="StaticPageSection", mappedBy="staticPageId", cascade={"persist"})
     *
     * @Assert\Valid
     */
    private $staticPageSections;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Title is required"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9 ]+$/i",
     *     htmlPattern=false,
     *     message="Title can contain only alphanumeric characters and spaces"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Slug is required"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9\-]+$/i",
     *     htmlPattern=false,
     *     message="Slug can contain only alphanumeric characters and '-'s"
     * )
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keyword", type="text", nullable=true)
     */
    private $metaKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
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
        $this->staticPageSections = new ArrayCollection();
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
     * Add staticPageSection
     *
     * @param  StaticPageSection $staticPageSection
     *
     * @return StaticPage
     */
    public function addStaticPageSection(StaticPageSection $staticPageSection)
    {
        $staticPageSection->setStaticPageId($this);
        
        $this->staticPageSections[] = $staticPageSection;
        
        return $this;
    }
    
    /**
     * Remove staticPageSection
     *
     * @param StaticPageSection $staticPageSection
     */
    public function removeStaticPageSection(StaticPageSection $staticPageSection)
    {
        $staticPageSection->setStatus(0);
    }
    
    /**
     * Get staticPageSections
     *
     * @return Collection
     */
    public function getStaticPageSections()
    {
        foreach ($this->staticPageSections as $thisStaticPageSection) {
            if (($thisStaticPageSection->getId()) && (! $thisStaticPageSection->getStatus())) {
                $this->staticPageSections->removeElement($thisStaticPageSection);
            }
        }
        
        return $this->staticPageSections;
    }

    /**
     * Set title
     *
     * @param  string     $title
     *
     * @return StaticPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param  string     $slug
     *
     * @return StaticPage
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set metaKeyword
     *
     * @param  string     $metaKeyword
     *
     * @return StaticPage
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;

        return $this;
    }

    /**
     * Get metaKeyword
     *
     * @return string
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    /**
     * Set metaDescription
     *
     * @param  string     $metaDescription
     *
     * @return StaticPage
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set status
     *
     * @param  integer    $status
     *
     * @return StaticPage
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param  \DateTime  $createdOn
     *
     * @return StaticPage
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
     * @param  integer    $createdBy
     *
     * @return StaticPage
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
     * @param  \DateTime  $lastModifiedOn
     *
     * @return StaticPage
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
     * @param  integer    $lastModifiedBy
     *
     * @return StaticPage
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
}
