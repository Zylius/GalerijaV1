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
                return $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                    "value" => $this->render('GalerijaImagesBundle:Default:single_image.html.twig', array(
                        'image' => $image,
                        'album' => $this->get('album_manager')->findById($albumId),
                    ))->getContent()
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

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("image".$id, $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
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

    public function editAction(Request $request, $imageId)
    {
        $im = $this->get("image_manager");
        $image = $im->findById($imageId);
        $form = $im->getEditForm($image);

        if($image == null || $image->getImageId() == 0)
        {
            $this->get('session')->getFlashBag()->add('error', 'Tokio paveiksliuko nerasta.');
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }
        if(!$this->get("user_extension")->belongsFilter($image))
        {
            $this->get('session')->getFlashBag()->add('error', 'Šis paveiksliukas nepriklauso jums');
            return $this->redirect($request->headers->get('referer'));
        }
        if ($request->isMethod('POST'))
        {
            if(!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
            {
                $this->get('session')->getFlashBag()->add('success', 'Redaguoti nuotraukas gali tik prisijungę vartotojai.');
                return $this->redirect($request->headers->get('referer'));
            }
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->get("album_manager")->recalculateCount($image);
                $this->get('session')->getFlashBag()->add('success', 'Nuotrauka sėkmingai atnaujinta.');
                return $this->redirect($request->headers->get('referer'));
            }
            else
            {
                $result = $this->get("errors")->getErrors($image);
                $this->get('session')->getFlashBag()->add('error',$result);
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('GalerijaImagesBundle:Forms:image_upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}