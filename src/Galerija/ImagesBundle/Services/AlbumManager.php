<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Form\Type\AlbumType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class AlbumManager
{
    protected $em;
    protected $formFactory;
    protected $router;
    protected $im;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router, ImageManager $im)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->im = $im;
    }
    public function getForm(Album $album)
    {
        return $this->formFactory->create(new AlbumType(), $album);
    }
    public function getEditForm(Album $album)
    {
        return $form = $this->formFactory->create(new AlbumType(),  $album, array(
                'action' => $this->router->generate('galerija_album_edit',
                    array('albumId' => $album->getAlbumId())),
            ));

    }
    public function save(Album $album)
    {
        $this->em->persist($album);
        $this->em->flush();
    }
    public function delete(Album $album)
    {
        $album->setDefaultImage(null);
        foreach($album->getImages() as $image)
        {
            $this->im->delete($image, $album);
        }

        $this->em->remove($album);
        $this->em->flush();
    }
    public function findAll()
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->findAll();
        foreach($value as $album)
        {
            $this->processDefaultImage($album);
        }
        return null === $value ? new Album() : $value;
    }
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->find($id);
        return null === $value ? new Album() : $value;
    }
    public function findAutoSelect(Album $album)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->findAutoSelect($album->getAlbumId());
        return $value;
    }
    public function findAlbumInArray($id, $arr)
    {
        foreach($arr as $album)
        {
            $aid = $album->getAlbumId();
            if($aid == $id)
                return true;
        }
        return false;
    }
    public function recalculateCount($image)
    {
        $arr = $this->em->getRepository('GalerijaImagesBundle:Album')->findAll();
        foreach($arr as $album)
        {
            $count = $album->getImages()->count();
            $album->setImageCount($count);
            if($album->getDefaultImage() == $image)
                $album->setDefaultImage(null);
        }
        $this->em->flush();

    }
    public function processDefaultImage(Album $album)
    {
       if($album->getDefaultImage() == null)
       {
           if($album->getImageCount() != 0)
           {
               if(!$album->getImages()->last())
               {
                   $this->recalculateCount(null);
               }
               else
               {
                    $album->setDefaultImage($album->getImages()->last());
                    return;
               }
           }
           $image = new Image();
           $image->setImageId("no_image");
           $image->setExt("png");
           $image->setPavadinimas("Albumas neturi nuotraukų.");
           $album->setDefaultImage($image);
       }
    }
}