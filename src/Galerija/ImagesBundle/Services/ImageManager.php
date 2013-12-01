<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class  Nuotraukų service'as
 * @package Galerija\ImagesBundle\Services
 */
class ImageManager
{
    protected $em;
    protected $formFactory;
    protected $router;

    /**
     * Konstruktorius nuotraukų service'ui.
     *
     * @param EntityManager $em ORM'o entity manageris
     * @param FormFactoryInterface $formFactory  sugeneruoja formas
     * @param Router $router router'is puslapių linkų generavimui
     */
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * Sukuriama forma nuotraukos sukūrimui
     *
     * @param Image $image Nuotrauka, pagal kurią kuriama forma
     * @param Album $album Albumas kuris pareikalavo formos
     * @return FormInterface
     */
    public function getForm(Image $image, Album $album = null)
    {
        return $this->formFactory->create(new ImageType(), $image, array(
                'action' => $this->router->generate(('galerija_images_upload'),
                ( array('albumId' => ($album == null ? 0 : $album->getAlbumId())) )
                ))
        );
    }

    /**
     * Sukuriama forma nuotraukos redagavimui
     *
     * @param Image $image Nuotrauka, pagal kurią kuriama nuotrauka
     * @return FormInterface
     */
    public function getEditForm(Image $image)
    {
        return
            $form = $this->formFactory->create(new ImageType('edit'), $image, array(
            'action' => $this->router->generate('galerija_image_edit',
                array('imageId' => $image->getImageId())),
            ));

    }

    /**
     * Išsaugomas paveiksliukas, padidinama jo albumų paveiksliuko skaitliuko vertė
     * Užklausos:
     *      INSERT INTO images (created, updated, pavadinimas, aprasymas, like_count, ext, shot_date, userId) VALUES (...) {...}
     *      UPDATE albums SET image_count = ?, updated = ? WHERE albumId = ?
     *      INSERT INTO albums_images (imageId, albumId) VALUES (?, ?)
     *
     * @param Image $image Išsaugoma nuotrauka
     */
    public function save(Image $image)
    {
        /* @var $album Album */
        foreach ($image->getAlbums() AS $album) {
            $album->setImageCount($album->getImageCount() + 1);
        }
        $this->em->persist($image);
        $this->em->flush();
    }

    /**
     * Pilnai pašalinamas paveiksliukas, ištrinami jo thumbnails'as, pati nuotrauka,
     * komentarai, like'ai. Albumų kuriam jis priklausė nuotraukų skaitliukas sumažinamas.
     *
     * Užklausos:  UPDATE albums SET image_count = image_count - 1, updated = laikas WHERE albumId = albumo_id
     *             DELETE FROM likes WHERE likeId = like_id
     *             DELETE FROM albums_images WHERE imageId = image_id
     *             DELETE FROM tags_images WHERE imageId = image_id
     *             DELETE FROM images WHERE imageId = image_id
     *
     * @param Image $image trinama nuotrauka
     */
    public function remove(Image $image)
    {
        if(file_exists($image->getAbsolutePath()))
        {
            unlink($image->getAbsolutePath());
        }

        $thumb_dir = __DIR__.'/../../../../web/media/cache/my_thumb/'.$image->getUploadDir().'/'.$image->getImageId().'.jpeg';

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

        /* @var $album Album */
        foreach ($image->getAlbums() AS $album) {
            $album->setImageCount($album->getImageCount() - 1);
            if($album->getDefaultImage() == $image)
                $album->setDefaultImage(null);
        }
        $this->em->remove($image);
    }

    /**
     * Pašalinamas paveiksliukas, priklausomai nuo to ar albumas yra nurodytas ar ne,
     * paveiksliukas gali būti pašalintas tik iš nurodytu albumo. Jei albumas nėra nurodytas,
     * albumas pašalinamas pilnai naudojantis this->remove() metodu
     *
     * Užklausos:  UPDATE albums SET image_count = image_count - 1, updated = laikas WHERE albumId = albumo_id
     *             DELETE FROM albums_images WHERE imageId = image_id
     *
     * @param Image $image trinama nuotrauka
     * @param Album $album albumas iš kurio trinama, null jei iš visų
     */
    public function delete(Image $image, Album $album = null)
    {
        if($album != null && $album->getAlbumId() != 0)
        {
            $image->removeAlbum($album);

            $album->setImageCount($album->getImageCount() - 1);

            if($album->getDefaultImage() == $image)
                $album->setDefaultImage(null);

            //jei nuotrauka nebeturi albumų, ją pilnai pašaliname.
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

    /**
     * Suranda paveiksliuką pagal ID
     *
     * Užklausa:
     * SELECT t0.imageId AS imageId1,
     * t0.created AS created2,
     * t0.updated AS updated3,
     * t0.pavadinimas AS pavadinimas4,
     * t0.aprasymas AS aprasymas5,
     * t0.like_count AS like_count6,
     * t0.ext AS ext7,
     * t0.shot_date AS shot_date8,
     * t0.userId AS userId9
     * FROM images t0
     * WHERE t0.imageId = ?
     *
     * @param int $id nuotraukos Id
     * @return Image nuotrauka
     */
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->find($id);
        return $value;
    }

    /**
     * Iš anksto užkraunami tag'ai pagal paveikslėlių masyvą (nebenaudojama)
     * (nesinaudojama findById, kad išvengti lazy loading)
     *
     * Užklausas galima rasti \Entity\ImageRepository
     *
     * @deprecated
     * @param ArrayCollection $images paveikslėlių masyvas
     * @return Array
     */
    public function preloadImages(ArrayCollection $images)
    {
        if ($images->count() == 0)
            return null;
        $image_ids = Array();
        /* @var $image Image */
        foreach($images as $image)
        {
            array_push($image_ids, $image->getImageId());
        }
        return $this->em->getRepository('GalerijaImagesBundle:Image')->preloadTags($image_ids);
    }

    /**
     * Gaunami paveiksliukai pagal vartotoją ir dabartinį puslapį
     * (nesinaudojama findById, kad išvengti lazy loading)
     *
     * Užklausas galima rasti \Entity\ImageRepository
     *
     * @param \Galerija\ImagesBundle\Entity\User $user vartotojas
     * @param int $page puslapio numeris
     * @return ArrayCollection rezultatai
     */
    public function findByUser(\Galerija\ImagesBundle\Entity\User $user, $page)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findForPageByUser($user->getId(), $page);
        return $value;
    }

    /**
     * Gaunami paveiksliukai pagal albumo id ir dabartinį puslapį
     * (nesinaudojama findById, kad išvengti lazy loading)
     *
     * Užklausas galima rasti \Entity\ImageRepository
     *
     * @param int $albumId albumo Id
     * @param int $page puslapio numeris
     * @return ArrayCollection rezultatai
     */
    public function findForPage($albumId,$page)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Image')->findForPageByAlbum($albumId,$page);
        return $value;
    }

    /**
     * Paveiksliuko įkėlimo procedūros, nustatomas failo tipas, jei nėra pavadinmo,
     * pakeičiama į failo pavadinimą, išsaugojus duomenų bazėj paveiksliukas perkeliamas į
     * tinkamą direktoriją.
     *
     * Užklausos:
     *
     * @param Image $image
     */
    public function uploadProcedures(Image $image)
    {
        /* @var $failas \Symfony\Component\HttpFoundation\File\UploadedFile */
        $failas = $image->getFailas();

        //išsaugom failo tipą
        $image->setExt($failas->guessExtension());

        //išsaugom originalų pavadinimą, jei jis nebuvo nurodytas
        if($image->getPavadinimas() == NULL)
            $image->setPavadinimas($failas->getClientOriginalName());

        $this->save($image);

        $failas->move($image->getUploadRootDir(), $image->getFileName());
    }
}