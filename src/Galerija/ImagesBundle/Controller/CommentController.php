<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Galerija\ImagesBundle\Entity\Comment;

/**
 * Class Komentarų kontroleris
 * @package Galerija\ImagesBundle\Controller
 */
class CommentController extends Controller
{

    /**
     * Sukuriamas naujas komentaras, patikrinama ar vartotojas prisijungęs, ar forma validi.
     * Jei ne surenkamos klaidos ir grąžinama informacija.
     *
     * @param Request $request užklausa
     * @return JsonResponse atsakas, apdorojamas PostCommentWidget.js faile
     */
    public function submitAction(Request $request)
    {
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

        $comment = new Comment();

        /* @var \Galerija\ImagesBundle\Services\CommentManager $cm  */
        $cm = $this->get("comment_manager");
        $form = $cm->getForm($comment);
        $form->handleRequest($request);

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
            return $response;
        }

        $response->setData(array(
            "success" => false,
            "message" => $this->get("errors")->getErrors($comment)
        ));
        return $response;
    }

    /**
     * Trinamas komentaras, patikrinama ar vartotojas prisijungęs, ar forma validi.
     * Kadangi šiuo atveju nesinaudojama Symfony formomis, patikrinamas gautas csf token'as.
     * Jei ne surenkamos klaidos ir grąžinama informacija
     *
     * @param Request $request užklausa
     * @return JsonResponse atsakas, apdorojamas DeleteWidget.js faile
     */
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();
        /* @var \Galerija\ImagesBundle\Services\CommentManager $cm  */
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
            "message" => 'Komentaras ištrintas sėkmingai.'
        ));
        return $response;

    }
}