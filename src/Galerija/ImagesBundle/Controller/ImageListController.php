<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
class ImageListController extends Controller
{
    public function albumByUserAction($userId, $page)
    {
        $user = $this->getDoctrine()->getRepository('GalerijaImagesBundle:User')->find($userId);
        $album = new Album();
        $album->setImages($this->get("image_manager")->findByUser($user, $page));
        $album->setShortComment($user->getUsername() . "o nuotraukos.");
        $album->setAlbumId(0);
        return $this->albumShow($album, $this->get('router')->generate('galerija_images_user_album', array('userId' => $userId, 'page' => $page + 1)));
    }

    public function albumByIdAction($albumId, $page)
    {
        $album = $this->get("album_manager")->findById($albumId);
        $album->setImages($this->get("image_manager")->findForPage($albumId, $page));
        return $this->albumShow($album, $this->get('router')->generate('galerija_images_album', array('albumId' => $albumId, 'page' => $page + 1)));
    }

    public function albumShow($album, $page)
    {
        //sukuriam  formą įkėlimui
        $image = new Image();

        //pridedam auto-select'ą
        $image->setAlbums($this->get("album_manager")->findAutoSelect($album));
        $user = $this->container->get('security.context')->getToken()->getUser();
        $tags = $this->get("tag_manager")->formatAllTags();
        return $this->render('GalerijaImagesBundle:Default:images.html.twig', array(
            'album' => $album,
            'form' => $this->get("image_manager")->getForm($image, $album)->createView(),
            'user' => $user,
            'tag_form' => $this->get("tag_manager")->getForm(new Tag())->createView(),
            'page' => $page,
            'tags' => $tags
        ));
    }
}
