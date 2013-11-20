<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Entity\Like;
use Galerija\ImagesBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
class ImageController extends Controller
{
    public function  likeAction($imageId)
    {
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');

        $response = new JsonResponse();


        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image');
        $image = $rep->find($imageId);
        $em = $this->getDoctrine()->getManager();
        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                "success" => false,
                "message" => '"Like\'inti" gali tik prisijungę vartotojai.'
            ));
            return $response;
        }
        $userId = $securityContext->getToken()->getUser()->getId();
        $liked = $rep->findLikedByImageUser($imageId, $userId);
        if($liked == true)
        {
            $image->setLikeCount($image->getLikeCount() - 1);
            $em->remove($liked[0]);
            $em->flush();
            $response->setData(array(
                "success" => true,
                "count" => $image->getLikeCount()
            ));
            return $response;
        }



        $like = new Like();
        $like->setImage($image);
        $like->setUser($securityContext->getToken()->getUser());

        $image->setLikeCount($image->getLikeCount() + 1);


        $em->persist($like);
        $em->persist($image);
        $em->flush();

        $response->setData(array(
            "success" => true,
            "count" => $image->getLikeCount()
        ));

        return $response;
    }
    public function  showInfoAction($imageId)
    {
        $securityContext = $this->container->get('security.context');
        $liked = false;
        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image');
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $userId = $securityContext->getToken()->getUser()->getId();
            $liked = $rep->findLikedByImageUser($imageId, $userId);
        }

        $image = $rep->find($imageId);

        $comment = new Comment();
        $comment->setImage($image);

        $commentform = $this->createForm(new CommentType(), $comment,array(
                'action' => $this->generateUrl('galerija_images_comment')
            ));
        $commentform->get('image')->setData($imageId);

        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Comment');
        $comment_array = $rep->findCommentsByImage($imageId);

        return $this->render('GalerijaImagesBundle:Default:image_info.html.twig', array(
            'image' => $image,
            'form' => $commentform->createView(),
            'comments' => $comment_array,
            'liked' => $liked
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

            $comment->setApproved(false);
            $comment->setUser($securityContext->getToken()->getUser());
            $comment->setImage($this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($form->get('image')->getData()));

            //įkeliam į db
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();


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