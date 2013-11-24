<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Album;
use Symfony\Component\HttpFoundation\Request;
class AlbumListController extends Controller
{
    public function indexAction(Request $request)
    {
        $album = new Album();
        $am = $this->get("album_manager");
        $form = $am->getForm($album);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $album_array = $am->findAll();

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $album->setUser($user);

                $am->save($album);

                $this->get('session')->getFlashBag()->add('success', 'Failas ?keltas s?kmingai!');
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

                //nustatom pranešim? ir parodom klientui
                $this->get('session')->getFlashBag()->add('error',$result);

                return $this->redirect($this->generateUrl('galerija_images_homepage'));
            }
        }

        return $this->render('GalerijaImagesBundle:Default:albums.html.twig', array(
            'album_array' => $album_array,
            'form' => $form->createView(),
            'user' => $user
        ));
    }
}