<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $image = new Image();
        $image->setAprasymas("Bandymas");
        $form = $this->createForm(new ImageType(), $image, array(
            'action' => $this->generateUrl('galerija_images_upload'),
        ));
        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image');
        $things = $rep->findAll();
        return $this->render('GalerijaImagesBundle:Default:index.html.twig', array(
            'image_array'      => $things,
            'form' => $form->createView()
        ));
    }

}