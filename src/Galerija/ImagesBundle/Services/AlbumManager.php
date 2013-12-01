<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Form\Type\AlbumType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Albumų service'as
 * @package Galerija\ImagesBundle\Services
 */
class AlbumManager
{
    protected $em;
    protected $formFactory;
    protected $router;
    protected $im;

    /**
     * Konstruktorius albumų service'ui.
     *
     * @param EntityManager $em ORM'o entity manageris
     * @param FormFactoryInterface $formFactory  sugeneruoja formas
     * @param Router $router router'is puslapių linkų generavimui
     * @param ImageManager $im image service'as albumo nuotraukų manipuliavimui
     */
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router, ImageManager $im)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->im = $im;
    }

    /**
     * Sukuriama forma albumo sukūrimui
     *
     * @param Album $album Albumas pagal kurį kuriama formą
     * @return FormInterface
     */
    public function getForm(Album $album)
    {
        return $this->formFactory->create(new AlbumType(), $album);
    }

    /**
     * Sukuriama forma albumo redagavimui
     *
     * @param Album $album Albumas pagal kurį kuriama formą
     * @return FormInterface
     */
    public function getEditForm(Album $album)
    {
        return $form = $this->formFactory->create(new AlbumType(),  $album, array(
                'action' => $this->router->generate('galerija_album_edit',
                    array('albumId' => $album->getAlbumId())),
            ));
    }

    /**
     * Išsaugomas albumas duom. bazėj
     *
     * @param Album $album Albumas kurį išsaugome
     */
    public function save(Album $album)
    {
        $this->em->persist($album);
        $this->em->flush();
    }

    /**
     * Ištrinamas albumas iš duom. bazės
     * Užklausa: DELETE FROM albums WHERE albumId = albumo_id
     *
     * @param Album $album kurį ištrinama
     */
    public function delete(Album $album)
    {
        //panaikiname titulinį paveikslą
        $album->setDefaultImage(null);

        //ištriname kiekvieną albumo paveikslą
        foreach($album->getImages() as $image)
        {
            $this->im->delete($image, $album);
        }

        $this->em->remove($album);
        $this->em->flush();
    }

    /**
     * Surandami visi albumai
     * Užklausa: SELECT
     *           t0.albumId AS albumId1,
     *           t0.short_comment AS short_comment2,
     *           t0.long_comment AS long_comment3,
     *           t0.auto_add AS auto_add4,
     *           t0.created AS created5,
     *           t0.updated AS updated6,
     *           t0.image_count AS image_count7,
     *           t0.userId AS userId8,
     *           t0.image_id AS image_id9
     *           FROM
     *           albums t0
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAll()
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->findAll();
        foreach($value as $album)
        {
            $this->processDefaultImage($album);
        }
        return $value;
    }

    /**
     * Surandamas albumas pagal id
     * Užklausa: SELECT
     *           t0.albumId AS albumId1,
     *           t0.short_comment AS short_comment2,
     *           t0.long_comment AS long_comment3,
     *           t0.auto_add AS auto_add4,
     *           t0.created AS created5,
     *           t0.updated AS updated6,
     *           t0.image_count AS image_count7,
     *           t0.userId AS userId8,
     *           t0.image_id AS image_id9
     *           FROM
     *           albums t0
     *           WHERE
     *           t0.albumId = $id
     *
     * @param int $id albumo ID
     * @return Album|null rastas albumas, null jei jis nerastas
     */
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->find($id);
        return $value;
    }

    /**
     * Suranda visus albumus kurie turėtų būti automatiškai pažymėti
     * Užklausa aprašyta \Entity\AlbumRepository.php
     *
     * @param Album $album albumas iš kurio kreipiamasi
     * @return \Doctrine\Common\Collections\ArrayCollection rasti albumai
     */
    public function findAutoSelect(Album $album)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Album')->findAutoSelect($album->getAlbumId());
        return $value;
    }

    /**
     * Suranda albumą pagal id masyve
     *
     * @param Array $arr albumų masyvas
     * @param int $id albumo id kurio ieškom
     * @return bool true, jeigu rastas
     */
    public function findAlbumInArray($id,Array $arr)
    {
        /* @var $album Album */
        foreach($arr as $album)
        {
            $aid = $album->getAlbumId();
            if($aid == $id)
                return true;
        }
        return false;
    }

    /**
     * Perskaičiuoja visų albumų nuotraukų kiekį ir nustato naują titulinį paveiksliuką, jei rekia
     *
     * @param Image $image redaguotas paveiksliukas, naudojamas titulinės nuotraukos tikrinimui
     */
    public function recalculateCount(Image $image = null)
    {
        $arr = $this->em->getRepository('GalerijaImagesBundle:Album')->findAll();

        /* @var $album Album */
        foreach($arr as $album)
        {
            $count = $album->getImages()->count();
            $album->setImageCount($count);
            if($album->getDefaultImage() == $image)
                $album->setDefaultImage(null);
        }
        $this->em->flush();

    }

    /**
     * Patikrina ar nustatytas teisingas titulinis paveiksliukas
     *
     * @param Album $album albumas kuriam tikriname titulinę nuotrauką
     */
    public function processDefaultImage(Album $album)
    {
       //jei titulinis paveiksliukas jau nustatytas nieko nedarom
       if($album->getDefaultImage() == null)
       {
           if($album->getImageCount() != 0)
           {
               if(!$album->getImages()->last())
               {
                   //perskaičiuojame paveiksliukų kiekį jei neradome nei vieno, nors albumo_image count rodo jog jų yra
                   $this->recalculateCount();
               }
               else
               {
                    //jei paveiksliukų yra, tituliniu nustatome paskutinį iš jų
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