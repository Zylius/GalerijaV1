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
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');
        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $this->get('session')->getFlashBag()->add('error','Įkelti nuotraukas gali tik prisijungę vartotojai.');
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }

        //susikuriam formą pagal kurią tikrinsim ar ji teisinga
        $image = new Image();
        $form = $this->createForm(new ImageType(), $image);
        $form->handleRequest($request);
        $image->setUser($this->container->get('security.context')->getToken()->getUser());

        //jei teisinga
        if($form->isValid())
        {
            //nustatom extension ir tinkamą pavadinimą
            $image->uploadProcedures();

            //įkeliam į db
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            //gavom teisingą ID, galima perkelti failą
            $image->getFailas()->move($image->getUploadRootDir(), $image->getFileName());

            //nustatom pranešimą ir parodom klientui
            $this->get('session')->getFlashBag()->add('success', 'Failas įkeltas sėkmingai!');
            return $this->redirect($this->generateUrl('galerija_images_homepage'));
        }


        //surandam klaidas
        $errors = $this->get('validator')->validate($image);
        $result = "";
        foreach( $errors as $error )
        {
            $result .= $error->getMessage();
        }

        //nustatom pranešimą ir parodom klientui
        $this->get('session')->getFlashBag()->add('error',$result);
        return $this->redirect($this->generateUrl('galerija_images_homepage'));
    }
}