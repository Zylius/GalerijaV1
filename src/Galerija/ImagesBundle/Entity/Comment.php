<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Komentarų klasė
 *
 * @ORM\Entity(repositoryClass="Galerija\ImagesBundle\Entity\CommentRepository")
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * Komentaro unikalus Id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $commentId;

    /**
     * Komentaro kūrėjas
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * Paveiksliukas kuriam priklauso komentaras
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="comments")
     * @ORM\JoinColumn(name="imageId", referencedColumnName="imageId")
     */
    protected $image;

    /**
     * Komentaro tekstas, negali būti trumpesnis nei 5 simboliai.
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *  message = "Komentaras negali būti tuščias"
     * )
     * @Assert\Length(min = "5")
     */
    protected $comment;

    /**
     * Ar patvirtintas
     *
     * @ORM\Column(type="boolean")
     */
    protected $approved;

    /**
     * Kada sukurtas
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
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
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
     * @param mixed $imageId
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @return mixed
     */
    public function getImageId()
    {
        return $this->imageId;
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

    /**
     * @param mixed $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Get commentId
     *
     * @return integer 
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * Set user
     *
     * @param \Galerija\ImagesBundle\Entity\User $user
     * @return Comment
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
     * Set image
     *
     * @param \Galerija\ImagesBundle\Entity\Image $image
     * @return Comment
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
}