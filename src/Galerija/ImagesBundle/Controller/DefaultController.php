<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
class DefaultController extends Controller
{
    public function indexAction()
    {
        //sukuriam  formą įkėlimui
        $image = new Image();
        $form = $this->createForm(new ImageType(), $image, array(
            'action' => $this->generateUrl('galerija_images_upload'),
        ));

        //surandam visas nuotraukas (vėliau bus pakeista į albumus)
        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image');
        $things = $rep->findAll();

        $token = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $user = $this->container->get('security.context')->getToken()->getUser();
        return $this->render('GalerijaImagesBundle:Default:index.html.twig', array(
            'image_array' => $things,
            'form' => $form->createView(),
            'token' => $token,
            'user' => $user
        ));
    }

}
