<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Zyleeee
 * Date: 13.10.16
 * Time: 16.55
 * To change this template use File | Settings | File Templates.
 */

namespace Galerija\ImagesBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Image
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $imageId;

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
     *  @ORM\Column(type="string")
     */
    protected $pavadinimas;

    /**
     * @ORM\Column(type="string")
     */
    protected $aprasymas;

    /**
     * @ORM\Column(length=10)
     */
    protected $ext;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $shot_date;

    /**
     * @ORM\ManyToMany(targetEntity="Album", inversedBy="images")
     * @ORM\JoinTable(name="albums_images",
     *      joinColumns={@ORM\JoinColumn(name="imageId", referencedColumnName="imageId")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="albumId", referencedColumnName="albumId")}
     *      )
     */
    protected $albums;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="image")
     */
    protected $comments;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="images")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $shot_date
     */
    public function setShotDate($shot_date)
    {
        $this->shot_date = $shot_date;
    }

    /**
     * @return mixed
     */
    public function getShotDate()
    {
        return $this->shot_date;
    }

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
     * @Assert\File(
     *     maxSizeMessage = "Paveikslėlis negali būti didesnis nei 5mB",
     *     maxSize = "5000k",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"},
     *     mimeTypesMessage = "Netinkamas nuotraukos tipas, turi būti JPG, PNG arba GIF"
     * )
     */
    protected $failas;

    /**
     * Get ID
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set pavadinimas
     *
     * @param string $pavadinimas
     * @return Image
     */
    public function setPavadinimas($pavadinimas)
    {
        $this->pavadinimas = $pavadinimas;
    
        return $this;
    }

    /**
     * Get pavadinimas
     *
     * @return string 
     */
    public function getPavadinimas()
    {
        return $this->pavadinimas;
    }

    /**
     * Set aprasymas
     *
     * @param string $aprasymas
     * @return Image
     */
    public function setAprasymas($aprasymas)
    {
        $this->aprasymas = $aprasymas;
    
        return $this;
    }

    /**
     * Get aprasymas
     *
     * @return string 
     */
    public function getAprasymas()
    {
        return $this->aprasymas;
    }

    /**
     * Set ext
     *
     * @param string $ext
     * @return Image
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    
        return $this;
    }

    /**
     * Get ext
     *
     * @return string 
     */
    public function getExt()
    {
        return $this->ext;
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


    public function getFailas()
    {
        return $this->failas;
    }

    public function setFailas($failas)
    {
        $this->failas = $failas;

        return $this;
    }

    /**
     * Add albums
     *
     * @param \Galerija\ImagesBundle\Entity\Album $albums
     * @return Image
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
     * Add comments
     *
     * @param \Galerija\ImagesBundle\Entity\Comment $comments
     * @return Image
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

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->albums = new ArrayCollection();
    }

    //vykdom įkėlimo procedūras
    public function uploadProcedures()
    {
        //išsaugom failo tipą
        $this->setExt($this->failas->guessExtension());

        //išsaugom originalų pavadinimą, jei jis nebuvo nurodytas
        if($this->pavadinimas == NULL)
            $this->pavadinimas = $this->failas->getClientOriginalName();
    }
    public function delete($em)
    {
        if(file_exists($this->getAbsolutePath()))
        {
            unlink($this->getAbsolutePath());
        }

        $thumb_dir = __DIR__.'/../../../../web/media/cache/my_thumb/'.$this->getUploadDir().'/' . $this->imageId . '.jpeg';
        if(file_exists($thumb_dir))
        {
            unlink($thumb_dir);
        }

        //pašalinam iš duomenų bazės
        $em->remove($this);
        $em->flush();
    }
    /*
    * gražina failo pavadinimą
    */
   public function getFileName()
   {
       return $this->imageId . "." . $this->ext;
   }
   /*
    * gražina pilna direktoriją su failo pavadinimu
    */
    public function getAbsolutePath()
    {
        return null === $this->getFileName() ? null : $this->getUploadRootDir().'/'.$this->getFileName();
    }
    /*
    * gražinam paveikslėlio direktoriją atvaizdavimui puslapy
    */
    public function getWebPath()
    {
        return null === $this->getFileName() ? null : $this->getUploadDir().'/'.$this->getFileName();
    }

    /*
    * absoliuti direktorija kur nuotrauka turėtų būt išsaugota
    *
    */
    public function getUploadRootDir()
    {

        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /*
    * atsikratom __DIR__, kad negadintu vaizdo
    */
    protected function getUploadDir()
    {

        return 'uploads';
    }

}