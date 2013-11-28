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
                "id" => $comment->getCommentId(),
                "delpath" => $this->get('templating.helper.assets')->getUrl("bundles/GalerijaImages/images/delete.png"),
                "username" => $comment->getUser()->getUserName(),
                "token" => $this->get('form.csrf_provider')->generateCsrfToken("comment".$comment->getCommentId())
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
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();

        $cm = $this->get("comment_manager");

        $id = (int)$request->request->get('ID');

        $comment = $cm->findById($id);

        if($comment == null)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio komentaro nerasta'
            ));
            return $response;
        }

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("comment".$id, $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
            ));
            return $response;
        }

        $image = $comment->getImage();

        if(!$this->get("user_extension")->belongsFilter($comment) && !$this->get("user_extension")->belongsFilter($image))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Galima trinti tik savo nuotraukų komentarus arba savo komentarus.'
            ));
            return $response;
        }

        $cm->remove($comment);

        $response->setData(array(
            "success" => true,
            "message" => 'Titulinė nuotrauka atnaujinta.'
        ));
        return $response;

    }
}