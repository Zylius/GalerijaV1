<?php
namespace Galerija\ImagesBundle\Services;
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
    public function getForm(Image $image, Album $album = null )
    {
        return $this->formFactory->create(new ImageType(), $image, array(
            'action' => $this->router->generate(('galerija_images_upload'),
            (array('albumId' => ($album == null ? 0 : $album->getAlbumId()) )
            )))
        );
    }
    public function save($image)
    {
        $this->em->persist($image);
        $this->em->flush();
    }
    public function remove(Image $image)
    {
        foreach ($image->getComments() AS $comment) {
            $this->em->remove($comment);
        }
        foreach ($image->getLikes() AS $like) {
            $this->em->remove($like);
        }
        $this->em->remove($image);
    }
    public function delete(Image $image,Album $album = null)
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
        if($album != null)
        {
            $image->removeAlbum($album);
            if($image->getAlbums()->count() == 0)
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
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->find($id);
        return $value;
    }
    public function findByUser(\Galerija\ImagesBundle\Entity\User $user)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findUserImages($user->getId());
        return $value;
    }
    public function formatTags(Image $image)
    {
        $tags = "";
        foreach ($image->getTags()->toArray() as $arr) {
            $tags .= ' tag-'.$arr->getName();
        }
        return $tags;
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