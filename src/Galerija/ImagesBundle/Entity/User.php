<?php
namespace Galerija\ImagesBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Vartotojo klasė
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * Unikalus ID
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Vartotojo komentarai
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */
    protected $comments;

    /**
     * Vartotojo paveiksliukai
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="user")
     */
    protected $images;

    /**
     * Vartotojo albumai
     *
     * @ORM\OneToMany(targetEntity="Album", mappedBy="user")
     */
    protected $albums;

    /**
     * Vartotojo like'ai
     *
     * @ORM\OneToMany(targetEntity="Like", mappedBy="user")
     */
    protected $likes;

    /**
     * Vartotojo tag'ai
     *
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="user")
     */
    protected $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->albums = new ArrayCollection();
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add comments
     *
     * @param \Galerija\ImagesBundle\Entity\Comment $comments
     * @return User
     */
    public function addComment(\Galerija\ImagesBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Galerija\ImagesBundle\Entity\Comment $comments
     */
    public function removeComment(\Galerija\ImagesBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add images
     *
     * @param \Galerija\ImagesBundle\Entity\Image $images
     * @return User
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
     * Add albums
     *
     * @param \Galerija\ImagesBundle\Entity\Album $albums
     * @return User
     */
    public function addAlbum(\Galerija\ImagesBundle\Entity\Album $albums)
    {
        $this->albums[] = $albums;
    
        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Galerija\ImagesBundle\Entity\Album $albums
     */
    public function removeAlbum(\Galerija\ImagesBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add tags
     *
     * @param \Galerija\ImagesBundle\Entity\Tag $tags
     * @return User
     */
    public function addTag(\Galerija\ImagesBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Galerija\ImagesBundle\Entity\Tag $tags
     */
    public function removeTag(\Galerija\ImagesBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}