<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $tagId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tags")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Image", mappedBy="tags")
     */
    protected $images;

    /**
     * @ORM\Column(type="string")
     */
    protected $short_comment;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get tagId
     *
     * @return integer 
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Set short_comment
     *
     * @param string $shortComment
     * @return Tag
     */
    public function setShortComment($shortComment)
    {
        $this->short_comment = $shortComment;
    
        return $this;
    }

    /**
     * Get short_comment
     *
     * @return string 
     */
    public function getShortComment()
    {
        return $this->short_comment;
    }

    /**
     * Set user
     *
     * @param \Galerija\ImagesBundle\Entity\User $user
     * @return Tag
     */
    public function setUser(\Galerija\ImagesBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Galerija\ImagesBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add images
     *
     * @param \Galerija\ImagesBundle\Entity\Image $images
     * @return Tag
     */
    public function addImage(\Galerija\ImagesBundle\Entity\Image $images)
    {
        $this->images[] = $images;
    
        return $this;
    }

    /**
     * Remove images
     *
     * @param \Galerija\ImagesBundle\Entity\Image $images
     */
    public function removeImage(\Galerija\ImagesBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
}