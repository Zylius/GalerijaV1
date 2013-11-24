<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
* @ORM\Entity(repositoryClass="Galerija\ImagesBundle\Entity\LikeRepository")
* @ORM\Table(name="likes")
*/
class Like
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $likeId;

    /**
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(type="datetime")
    */
    private $created;

    /**
    * @Gedmo\Timestampable(on="update")
    * @ORM\Column(type="datetime")
    */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="likes")
     * @ORM\JoinColumn(name="imageId", referencedColumnName="imageId")
     */
    protected $image;

    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="likes")
    * @ORM\JoinColumn(name="userId", referencedColumnName="id")
    */
    protected $user;

    /**
     * Get likeId
     *
     * @return integer 
     */
    public function getLikeId()
    {
        return $this->likeId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Like
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Like
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set image
     *
     * @param \Galerija\ImagesBundle\Entity\Image $image
     * @return Like
     */
    public function setImage(\Galerija\ImagesBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Galerija\ImagesBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set user
     *
     * @param \Galerija\ImagesBundle\Entity\User $user
     * @return Like
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
}