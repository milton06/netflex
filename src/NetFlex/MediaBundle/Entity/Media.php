<?php

namespace NetFlex\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="medias")
 * @ORM\Entity(repositoryClass="NetFlex\MediaBundle\Repository\MediaRepository")
 */
class Media
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
     * @var string
     *
     * @ORM\Column(name="media_name", type="string", length=255)
     */
    private $mediaName;

    /**
     * @var string
     *
     * @ORM\Column(name="media_extension", type="string", length=255)
     */
    private $mediaExtension;


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
     * Set mediaName
     *
     * @param string $mediaName
     *
     * @return Media
     */
    public function setMediaName($mediaName)
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    /**
     * Get mediaName
     *
     * @return string
     */
    public function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Set mediaExtension
     *
     * @param string $mediaExtension
     *
     * @return Media
     */
    public function setMediaExtension($mediaExtension)
    {
        $this->mediaExtension = $mediaExtension;

        return $this;
    }

    /**
     * Get mediaExtension
     *
     * @return string
     */
    public function getMediaExtension()
    {
        return $this->mediaExtension;
    }
}
