<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Tag'ų klasė
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag
{
    /**
     * Unikalus tag'o ID
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $tagId;

    /**
     * Tag'o kurėjas
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tags")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * Paveiksliukai kuriems priskirtas šitas tag'as
     *
     * @ORM\ManyToMany(targetEntity="Image", mappedBy="tags")
     */
    protected $images;

    /**
     * Tag'o pavadinimas
     *
     * @ORM\Column(type="string")
     */
    protected $name;

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

    /**
     * Set name
     *
     * @param string $name
     * @return Tag
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
}