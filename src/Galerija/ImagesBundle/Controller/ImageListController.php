<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
class ImageListController extends Controller
{
    public function albumByUserAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('GalerijaImagesBundle:User')->find($userId);
        $album = new Album();
        $album->setImages($this->get("image_manager")->findByUser($user));
        $album->setShortComment($user->getUsername() . "o nuotraukos.");
        $album->setAlbumId(0);
        return $this->albumShow($album);
    }

    public function albumByIdAction($albumId)
    {
        $album = $this->get("album_manager")->findById($albumId);
        return $this->albumShow($album);
    }

    public function albumShow($album)
    {
        //sukuriam  formą įkėlimui
        $image = new Image();

        //pridedam auto-select'ą
        $image->setAlbums($this->get("album_manager")->findAutoSelect($album));
        $user = $this->container->get('security.context')->getToken()->getUser();
        $this->get('image_manager')->preloadImages($album->getImages());
        return $this->render('GalerijaImagesBundle:Default:images.html.twig', array(
            'album' => $album,
            'form' => $this->get("image_manager")->getForm($image, $album)->createView(),
            'user' => $user,
            'tag_form' => $this->get("tag_manager")->getForm(new Tag())->createView()
        ));
    }
}
