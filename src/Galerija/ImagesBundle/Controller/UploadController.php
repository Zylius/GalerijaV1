<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class UploadController extends Controller
{
    public function indexAction(Request $request)
    {
        $image = new Image();
        $image->setAprasymas("Bandymas");
        $form = $this->createForm(new ImageType(), $image, array(
            'action' => $this->generateUrl('galerija_images_upload'),
        ));
        $form->handleRequest($request);
        if($form->isvalid())
        {
            $new_image_file = $form['pavadinimas']->getData();
            $image->setPavadinimas($new_image_file->getClientOriginalName());
            $image->setExt($new_image_file->guessExtension());
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $new_image_file->move($image->getUploadRootDir(), $image->getFileName());
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }
        return $this->redirect($this->generateUrl('galerija_images_homepage'));
    }
}