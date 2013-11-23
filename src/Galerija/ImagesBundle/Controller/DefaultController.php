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
    public function tagAction(Request $request)
    {
        $tm = $this->get("tag_manager");

        $form = $tm->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tm->save();
            $this->get('session')->getFlashBag()->add('success', 'Tag\'as sukurtas sėkmingai!');
            return $this->redirect($request->headers->get('referer'));
        }
    }
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

        return $this->render('GalerijaImagesBundle:Default:albums.html.twig', array(
            'album_array' => $album_array,
            'form' => $form->createView(),
            'user' => $user,
            'token' => $token
        ));


    }

    public function albumByUserAction($userId)
    {

        $user = $this->getDoctrine()->getRepository('GalerijaImagesBundle:User')->find($userId);
        $album = new Album();
        $rep = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Album');
        $album->setImages($rep->findUserImages($userId));
        $album->setShortComment($user->getUsername() . "o nuotraukos.");
        return $this->albumShow($album);
    }

    public function albumByIdAction($albumId)
    {
        $album = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Album')->find($albumId);
        return $this->albumShow($album);
    }

    public function albumShow($album)
    {
        //sukuriam  formą įkėlimui
        $image = new Image();

        //pridedam auto-select'ą
        $image->setAlbums($this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->findAutoSelect($album->getAlbumId()));

        $form = $this->createForm(new ImageType(), $image, array(
                'action' => $this->generateUrl(('galerija_images_upload'),
                array('albumId' => $album->getAlbumId()))
        ));

        $tm = $this->get("tag_manager");

        $token = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $user = $this->container->get('security.context')->getToken()->getUser();

        return $this->render('GalerijaImagesBundle:Default:images.html.twig', array(
            'album' => $album,
            'form' => $form->createView(),
            'token' => $token,
            'user' => $user,
            'tag_form' => $tm->getForm()->createView()
        ));
    }
}
