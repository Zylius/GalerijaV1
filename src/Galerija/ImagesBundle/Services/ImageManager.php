<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class ImageManager
{
    protected $em;
    protected $formFactory;
    protected $router;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }
    public function getForm(Image $image, Album $album = null)
    {
        return $this->formFactory->create(new ImageType(), $image, array(
                'action' => $this->router->generate(('galerija_images_upload'),
                ( array('albumId' => ($album == null ? 0 : $album->getAlbumId())) )
                ))
        );
    }

    public function getEditForm(Image $image, Album $album = null)
    {
        return
            $form = $this->formFactory->create(new ImageType('edit'), $image, array(
            'action' => $this->router->generate('galerija_image_edit',
                array('imageId' => $image->getImageId())),
            ));

    }
    public function save($image)
    {
        foreach ($image->getAlbums() AS $album) {
            $album->setImageCount($album->getImageCount() + 1);
        }
        $this->em->persist($image);
        $this->em->flush();
    }
    public function remove(Image $image)
    {
        if(file_exists($image->getAbsolutePath()))
        {
            unlink($image->getAbsolutePath());
        }

        $thumb_dir = __DIR__.'/../../../../web/media/cache/my_thumb/'.$image->getUploadDir().'/' . $image->getImageId() . '.jpeg';
        if(file_exists($thumb_dir))
        {
            unlink($thumb_dir);
        }
        foreach ($image->getComments() AS $comment) {
            $this->em->remove($comment);
        }
        foreach ($image->getLikes() AS $like) {
            $this->em->remove($like);
        }
        foreach ($image->getAlbums() AS $album) {
            $album->setImageCount($album->getImageCount() - 1);
            if($album->getDefaultImage() == $image)
                $album->setDefaultImage(null);
        }
        $this->em->remove($image);
    }
    public function delete(Image $image,Album $album = null)
    {
        if($album != null && $album->getAlbumId() != 0)
        {
            $image->removeAlbum($album);

            $album->setImageCount($album->getImageCount() - 1);
            $album->setDefaultImage(null);

            $count = $image->getAlbums()->count();
            if($count == 0)
            {
                $this->remove($image);
            }
        }
        else
        {
            $this->remove($image);
        }
        $this->em->flush();
    }
    public function findAll()
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findAll();
        return $value;
    }
    public function preloadImages($images)
    {
        if ($images->count() == 0)
            return null;
        $image_ids = Array();
        foreach($images as $image)
        {
            array_push($image_ids, $image->getImageId());
        }
        return $this->em->getRepository('GalerijaImagesBundle:Image')->preloadTags($image_ids);
    }
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->find($id);
        return $value;
    }
    public function findByUser(\Galerija\ImagesBundle\Entity\User $user, $page)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findForPageByUser($user->getId(), $page);
        return $value;
    }
    public function findForPage($albumId,$page)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findForPageByAlbum($albumId,$page);
        return $value;
    }
    public function uploadProcedures(Image $image)
    {
        //išsaugom failo tipą
        $image->setExt($image->getFailas()->guessExtension());

        //išsaugom originalų pavadinimą, jei jis nebuvo nurodytas
        if($image->getPavadinimas() == NULL)
            $image->setPavadinimas($image->getFailas()->getClientOriginalName());

        $this->save($image);

        $image->getFailas()->move($image->getUploadRootDir(), $image->getFileName());
    }
}