<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
class ImageController extends Controller
{
    public function  showInfoAction($imageId)
    {
        $securityContext = $this->container->get('security.context');
        $liked = false;
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $userId = $securityContext->getToken()->getUser()->getId();
            $liked = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Like')->findLikesByImageUser($imageId, $userId);
        }

        $image = $this->get("image_manager")->findById($imageId);

        $comment = new Comment();
        $comment->setImage($image);

        $comment_array = $this->get("comment_manager")->findByImage($image);

        return $this->render('GalerijaImagesBundle:Default:image_info.html.twig', array(
            'image' => $image,
            'form' => $this->get("comment_manager")->GetForm($comment,$image)->createView(),
            'comments' => $comment_array,
            'liked' => $liked
        ));
    }
    public function uploadAction(Request $request, $albumId)
    {
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');

        $response = new JsonResponse();

        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Įkelti nuotraukas gali tik prisijungę vartotojai.'
            ));
            return $response;
        }

        //susikuriam formą pagal kurią tikrinsim ar ji teisinga
        $im = $this->get("image_manager");
        $image = new Image();
        $form = $im->getForm($image);
        $form->handleRequest($request);
        $image->setUser($this->container->get('security.context')->getToken()->getUser());

        //jei teisinga
        if($form->isValid())
        {

            $im->uploadProcedures($image);

            //patikrinam ar albumas iš kurio buvo įkelta buvo įtrauktas į šios nuotraukos albumų sąrašą
            if($albumId == 0 || $this->get('album_manager')->findAlbumInArray($albumId, $image->getAlbums()->toArray()))
            {
                $assetManager = $this->get('templating.helper.assets');
                $cacheManager = $this->container->get('liip_imagine.cache.manager');
                $this->container->get('liip_imagine.controller')->filterAction($this->getRequest(),$image->getWebPath(),'my_thumb');
                $srcPath = $cacheManager->getBrowserPath($image->getWebPath(), 'my_thumb');

                $response->setData(array(
                    "success" => true,
                    "tags" => $this->get("tag_manager")->formatTags($image),
                    "message" => 'Failas įkeltas sėkmingai!',
                    "thumb_path" =>  $assetManager->getUrl($srcPath),
                    "path" =>  $this->generateUrl(('galerija_images_image_info'), array('imageId' => $image->getImageId())),
                    "delpath" =>  $assetManager->getUrl("bundles/GalerijaImages/images/delete.png"),
                    "name" =>  $image->getPavadinimas(),
                    "ID" => $image->getImageId()
                ));
            }
            else
            {
                $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                ));
            }
            //nustatom pranešimą ir parodom klientui
            return $response;
        }


        $result = $this->get("errors")->getErrors($image);
        //kai php.ini failo dydis viršijamas, gražinamas tuščias klaidų sąrašas
        if($result == "")
            $result = "Failas per didelis";
        //nustatom pranešimą ir parodom klientui
        $response->setData(array(
            "success" => false,
            "message" => $result
        ));
        return $response;
    }
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();

        $im = $this->get("image_manager");

        $id = (int)$request->request->get('ID');
        $aid = (int)$request->request->get('aID');

        $image = $im->findById($id);

        if($image == null)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio paveiksliuko nerasta'
            ));
            return $response;
        }

        if(!$this->get("user_extension")->belongsFilter($image))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Galima trinti tik savo nuotraukas.'
            ));
            return $response;
        }

        $im->delete($image,  $this->get("album_manager")->findById($aid));

        $response->setData(array(
            "success" => true,
            "message" => 'Failas ištrintas sėkmingai!'
        ));
        return $response;

    }
}