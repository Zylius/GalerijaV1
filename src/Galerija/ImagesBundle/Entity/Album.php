<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Albumų klasė
 *
 * @ORM\Entity(repositoryClass="Galerija\ImagesBundle\Entity\AlbumRepository")
 * @ORM\Table(name="albums")
 */
class Album
{

    /**
     * Albumo unikalus Id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $albumId;

    /**
     * Albumo kūrėjas
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="albums")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * Paveiksliukai priklausantys albumui
     *
     * @ORM\ManyToMany(targetEntity="Image", mappedBy="albums")
     */
    protected $images;

    /**
     * Trumpas komentaras (pavadinimas)
     *
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      max = "50",
     *      maxMessage = "Pavadinimas turi būti trumpesnis nei {{ limit }} simbolių"
     * )
     */
    protected $short_comment;

    /**
     * Ilgas komentaras
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      max = "200",
     *      maxMessage = "Aprašymas turi būti trumpesnis nei {{ limit }} simbolių"
     * )
     */
    protected $long_comment;

    /**
     * Ar automatiškai pažymim albumą keliant paviksliuką
     *
     * @ORM\Column(type="boolean")
     */
    protected $auto_add;

    /**
     * Kada alumas sukurtas
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;

    /**
     * Kada atnaujintas
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    /**
     * Paveiksliukų skaitiklis albume
     *
     * @ORM\Column(type="integer")
     */
    protected $image_count;

    /**
     * Titulinis albumo paveiksliukas
     *
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="imageId")
     */
    protected $defaultImage;

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $albumId
     */
    public function setAlbumId($albumId)
    {
        $this->albumId = $albumId;
    }

    /**
     * @return mixed
     */
    public function getAlbumId()
    {
        return $this->albumId;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
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

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function __construct()
    {
        $this->defaultImage = null;
        $this->image_count = 0;
        $this->albumId = 0;
        $this->images = new ArrayCollection();
    }

    /**
     * Set short_comment
     *
     * @param string $shortComment
     * @return Album
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
     * Set long_comment
     *
     * @param string $longComment
     * @return Album
     */
    public function setLongComment($longComment)
    {
        $this->long_comment = $longComment;
    
        return $this;
    }

    /**
     * Get long_comment
     *
     * @return string 
     */
    public function getLongComment()
    {
        return $this->long_comment;
    }

    /**
     * Set user
     *
     * @param \Galerija\ImagesBundle\Entity\User $user
     * @return Album
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
     * @return Album
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
     * Set auto_add
     *
     * @param boolean $autoAdd
     * @return Album
     */
    public function setAutoAdd($autoAdd)
    {
        $this->auto_add = $autoAdd;
    
        return $this;
    }

    /**
     * Get auto_add
     *
     * @return boolean 
     */
    public function getAutoAdd()
    {
        return $this->auto_add;
    }


    /**
     * Set image_count
     *
     * @param integer $imageCount
     * @return Album
     */
    public function setImageCount($imageCount)
    {
        $this->image_count = $imageCount;
    
        return $this;
    }

    /**
     * Get image_count
     *
     * @return integer 
     */
    public function getImageCount()
    {
        return $this->image_count;
    }


    /**
     * Set defaultImage
     *
     * @param \Galerija\ImagesBundle\Entity\Image $defaultImage
     * @return Album
     */
    public function setDefaultImage(\Galerija\ImagesBundle\Entity\Image $defaultImage = null)
    {
        $this->defaultImage = $defaultImage;
    
        return $this;
    }

    /**
     * Get defaultImage
     *
     * @return \Galerija\ImagesBundle\Entity\Image 
     */
    public function getDefaultImage()
    {
        return $this->defaultImage;
    }
}