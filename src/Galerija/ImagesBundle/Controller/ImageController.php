<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Form\Type\CommentType;
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

        $commentform = $this->createForm(new CommentType(), $comment);
        return $this->render('GalerijaImagesBundle:Default:image_info.html.twig', array(
            'image' => $image,
            'form' => $commentform->createView()
        ));
    }
}