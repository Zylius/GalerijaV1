<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
class ImageController extends Controller
{
    public function  showInfoAction($imageId)
    {
        $image = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($imageId);
        $securityContext = $this->container->get('security.context');
        $comment = new Comment();
        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $user = $securityContext->getToken()->getUser();
            $comment->setUser($user);
        }

        $commentform = $this->createForm(new CommentType(), $comment,array(
                'action' => $this->generateUrl('galerija_images_comment')
            ));
        return $this->render('GalerijaImagesBundle:Default:image_info.html.twig', array(
            'image' => $image,
            'form' => $commentform->createView()
        ));
    }
    public function commentAction(Request $request)
    {
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');

        $response = new JsonResponse();

        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                    "success" => false,
                    "message" => 'Komentuoti gali tik prisijungę vartotojai.'
                ));
            return $response;
        }

        //susikuriam formą pagal kurią tikrinsim ar ji teisinga
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($request);

        //jei teisinga
        if($form->isValid())
        {
            //įkeliam į db
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

                $response->setData(array(
                        "success" => true,
                        "message" => 'Komentaras pridėtas!',
                        "value" => $comment->getComment(),
                        "time" => new \DateTime($comment->getCreated()),
                        "username" => $comment->getUser()->getUserName()
                ));
            //nustatom pranešimą ir parodom klientui
            return $response;
        }


        //surandam klaidas
        $errors = $this->get('validator')->validate($comment);
        $result = "";
        foreach( $errors as $error )
        {
            $result .= $error->getMessage();
        }

        //nustatom pranešimą ir parodom klientui
        $response->setData(array(
                "success" => false,
                "message" => $result
            ));
        return $response;
    }
}