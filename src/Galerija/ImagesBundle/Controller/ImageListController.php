<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Nuotraukų sąrašo (list'o) kontroleris
 *
 * @package Galerija\ImagesBundle\Controller
 */
class ImageListController extends Controller
{

    /**
     * Atvaizduoja nuotruakų sąrašą pagal vartotoją.
     * Patikrinama ar toks useris egzistuoja, jei ne, vartotojas grąžinamas į pradinį puslapį
     *
     * @param int $userId vartotojo Id
     * @param int $page esamas (nuradoytas) puslapis
     * @return mixed jei viskas sėkmingai, grąžinamas sugeneruotas puslapis, jei ne nurodymas į homepage'ą
     */
    public function albumByUserAction($userId, $page)
    {
        $user = $this->getDoctrine()->getRepository('GalerijaImagesBundle:User')->find($userId);

        if(!$user)
        {
            $this->get('session')->getFlashBag()->add('error', 'Toks vartotojas neegizstuoja.');
            return new RedirectResponse($this->get('router')->generate('galerija_album_homepage'));
        }

        $album = new Album();
        $album->setImages($this->get("image_manager")->findByUser($user, $page));
        $album->setShortComment($user->getUsername() . "o nuotraukos.");
        $album->setAlbumId(0);

        return $this->albumShow($album, $this->get('router')->generate('galerija_images_user',
            array('userId' => $userId, 'page' => $page + 1)));
    }

    /**
     * Atvaizduoja nuotruakų sąrašą pagal albumąą.
     * Patikrinama ar toks albumas egzistuoja, jei ne, vartotojas grąžinamas į pradinį puslapį
     *
     * @param int $albumId albumo Id
     * @param int $page esamas (nuradoytas) puslapis
     * @return mixed jei viskas sėkmingai, grąžinamas sugeneruotas puslapis, jei ne nurodymas į homepage'ą
     */
    public function albumByIdAction($albumId, $page)
    {
        $album = $this->get("album_manager")->findById($albumId);
        if(!$album)
        {
            $this->get('session')->getFlashBag()->add('error', 'Toks albumas neegizstuoja.');
            return new RedirectResponse($this->get('router')->generate('galerija_album_homepage'));
        }
        $album->setImages($this->get("image_manager")->findForPage($albumId, $page));
        return $this->albumShow($album, $this->get('router')->generate('galerija_images_album',
            array('albumId' => $albumId, 'page' => $page + 1)));
    }

    /**
     * Atvaizduojamas albumas, jei albumas yra tuščias, parodoma žinutė
     *
     * @param Album $album albumas kurį atvaizduojame
     * @param int $page kito puslapio linkas
     * @return mixed sugeneruotas puslapis
     */
    public function albumShow(Album $album, $page)
    {
        $image = new Image();
        if($album->getImages()->count() == 0)
        {
            $this->get('session')->getFlashBag()->add('success', 'Albumas tuščias.');
        }
        $image->setAlbums($this->get("album_manager")->findAutoSelect($album));
        $user = $this->container->get('security.context')->getToken()->getUser();
        $tags = $this->get("tag_manager")->formatAllTags();
        return $this->render('GalerijaImagesBundle:Images:images.html.twig', array(
            'album' => $album,
            'form' => $this->get("image_manager")->getForm($image, $album)->createView(),
            'user' => $user,
            'tag_form' => $this->get("tag_manager")->getForm(new Tag())->createView(),
            'page' => $page,
            'tags' => $tags
        ));
    }
}
