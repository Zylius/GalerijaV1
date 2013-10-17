<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class DeleteController extends Controller
{
    public function indexAction(Request $request)
    {
        $image = new Image();
        $image->setAprasymas("Bandymas");
        $id = $request->request->get('ID');
        if($image = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($id))
        {
            if(file_exists($image->getAbsolutePath()))
            {
                unlink($image->getAbsolutePath());
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }
        return $this->redirect($this->generateUrl('galerija_images_homepage'));
    }
}