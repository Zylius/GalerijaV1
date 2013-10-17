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
/**
 * @ORM\Entity
 * @ORM\Table(name="nuotraukos")
 */
class Image
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ID;

    /**
     * @ORM\Column(type="text")
     */
    protected $pavadinimas;

    /**
     * @ORM\Column(type="text")
     */
    protected $aprasymas;

    /**
     * @ORM\Column(type="text")
     */
    protected $ext;

    /**
     * Get ID
     *
     * @return integer 
     */
    public function getID()
    {
        return $this->ID;
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

    public function getFileName()
    {
        return $this->ID . "." . $this->ext;
    }
    public function getAbsolutePath()
    {
        return null === $this->getFileName() ? null : $this->getUploadRootDir().'/'.$this->getFileName();
    }

    public function getWebPath()
    {
        return null === $this->getFileName() ? null : $this->getUploadDir().'/'.$this->getFileName();
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads';
    }

}