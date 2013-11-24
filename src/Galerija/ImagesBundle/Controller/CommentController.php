<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Galerija\ImagesBundle\Entity\Comment;

class CommentController extends Controller
{
    public function submitAction(Request $request)
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

        $cm = $this->get("comment_manager");

        $form = $cm->getForm($comment);

        $form->handleRequest($request);

        //jei teisinga
        if($form->isValid())
        {

            $comment->setApproved(false);
            $comment->setUser($securityContext->getToken()->getUser());
            $comment->setImage($this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($form->get('image')->getData()));

            $cm->save($comment);

            $response->setData(array(
                "success" => true,
                "message" => 'Komentaras pridėtas!',
                "value" => $comment->getComment(),
                "time" => $comment->getCreated(),
                "username" => $comment->getUser()->getUserName()
            ));
            //nustatom pranešimą ir parodom klientui
            return $response;
        }

        //nustatom pranešimą ir parodom klientui
        $response->setData(array(
            "success" => false,
            "message" => $this->get("errors")->getErrors($comment)
        ));
        return $response;
    }
}