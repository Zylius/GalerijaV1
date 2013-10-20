<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
class DeleteController extends Controller
{
    public function indexAction(Request $request)
    {
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');
        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $this->get('session')->getFlashBag()->add('error','Trinti nuotraukas gali tik prisijungę vartotojai.');
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }

        //paimam trinamos nuotrakos ID ir patikrinam ar tokia yra db
        $id = $request->request->get('ID');
        if($image = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($id))
        {
            //patikrinam ar failas egzistuoja ir ištrinam
            if(file_exists($image->getAbsolutePath()))
            {
                unlink($image->getAbsolutePath());
            }

            //pašalinam iš duomenų bazės
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            //nustatom pranešimą ir parodom klientui
            $this->get('session')->getFlashBag()->add('success','Failas ištrintas sėkmingai!');
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }

        $this->get('session')->getFlashBag()->add('error','Failo ištrinti nepavyko');
        return $this->redirect($this->generateUrl('galerija_images_homepage'));
    }
}