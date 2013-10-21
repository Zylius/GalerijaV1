<?php
namespace Galerija\ImagesBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="userId")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="userId")
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="userId")
     */
    protected $albums;

    /**
     * @param mixed $albums
     */
    public function setAlbums($albums)
    {
        $this->albums = $albums;
    }

    /**
     * @return mixed
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->albums = new ArrayCollection();
        parent::__construct();
        // your own logic
    }
}