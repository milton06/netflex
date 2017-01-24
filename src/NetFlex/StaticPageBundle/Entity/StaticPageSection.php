<?php

namespace NetFlex\StaticPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NetFlex\StaticPageBundle\Entity\StaticPage;

/**
 * StaticPageSection
 *
 * @ORM\Table(name="static_page_sections")
 * @ORM\Entity(repositoryClass="NetFlex\StaticPageBundle\Repository\StaticPageSectionRepository")
 */
class StaticPageSection
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
     * @ORM\ManyToOne(targetEntity="StaticPage", inversedBy="staticPageSections")
     * @ORM\JoinColumn(name="static_page_id", referencedColumnName="id")
     */
    private $staticPageId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank(
     *     message="Content is required"
     * )
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="smallint")
     *
     * @Assert\NotBlank(
     *     message="Position is required"
     * )
     */
    private $position;

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
     * Set staticPageId
     *
     * @param  StaticPage        $staticPageId
     *
     * @return StaticPageSection
     */
    public function setStaticPageId(StaticPage $staticPageId = null)
    {
        $this->staticPageId = $staticPageId;
        
        return $this;
    }
    
    /**
     * Get staticPageId
     *
     * @return StaticPage
     */
    public function getStaticPageId()
    {
        return $this->staticPageId;
    }

    /**
     * Set content
     *
     * @param  string            $content
     *
     * @return StaticPageSection
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set position
     *
     * @param  integer           $position
     *
     * @return StaticPageSection
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set status
     *
     * @param  boolean           $status
     *
     * @return StaticPageSection
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
