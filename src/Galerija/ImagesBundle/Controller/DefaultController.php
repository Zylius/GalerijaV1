<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Form\Type\AlbumType;
use Symfony\Component\HttpFoundation\Request;
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        //sukuriam  formą Albumui
        $album = new Album();
        $form = $this->createForm(new AlbumType(), $album);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $token = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');


        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Album');
        $album_array = $rep->findAll();
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $album->setUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($album);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Failas įkeltas sėkmingai!');
                return $this->redirect($this->generateUrl('galerija_images_homepage'));
            }
            else
            {
                //surandam klaidas
                $errors = $this->get('validator')->validate($form);
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

        return $this->render('GalerijaImagesBundle:Default:index.html.twig', array(
            'album_array' => $album_array,
            'form' => $form->createView(),
            'user' => $user,
            'token' => $token
        ));


    }
    public function imageAction($albumId)
    {
        //sukuriam  formą įkėlimui
        $image = new Image();
        $form = $this->createForm(new ImageType(), $image, array(
            'action' => $this->generateUrl('galerija_images_upload'),
        ));


        //surandam visas nuotraukas (vėliau bus pakeista į albumus)
        $album = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Album')->find($albumId);
        $image_array = $album->getImages();
        $image->addAlbum($album);


        $token = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $user = $this->container->get('security.context')->getToken()->getUser();

        return $this->render('GalerijaImagesBundle:Default:index.html.twig', array(
            'image_array' => $image_array,
            'form' => $form->createView(),
            'token' => $token,
            'user' => $user
        ));
    }
}
