<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Album;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
class AlbumListController extends Controller
{
    public function indexAction(Request $request)
    {
        $album = new Album();
        $am = $this->get("album_manager");
        $form = $am->getForm($album);
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $album->setUser($user);

                $am->save($album);

                $this->get('session')->getFlashBag()->add('success', 'Albumas sukurtas sėkmingai!');
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
        $album_array = $am->findAll();
        return $this->render('GalerijaImagesBundle:Default:albums.html.twig', array(
            'album_array' => $album_array,
            'form' => $form->createView(),
            'user' => $user
        ));
    }
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();

        $am = $this->get("album_manager");

        $aid = (int)$request->request->get('ID');

        $album = $am->findById($aid);

        if($album == null)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio albumo nerasta'
            ));
            return $response;
        }

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("album".$aid, $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
            ));
            return $response;
        }

        if(!$this->get("user_extension")->belongsFilter($album))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Galima trinti tik savo albumus.'
            ));
            return $response;
        }

        $am->delete($album);

        $response->setData(array(
            "success" => true,
            "message" => 'Albumas ištrintas sėkmingai!'
        ));
        return $response;
    }
    public function setDefaultAction(Request $request)
    {
        $response = new JsonResponse();
        $aid = (int)$request->request->get('aID');
        $id = (int)$request->request->get('ID');

        $album = $this->get("album_manager")->findById($aid);
        $image = $this->get("image_manager")->findById($id);
        if($album == null || $album->getAlbumId() == 0)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio albumo nerasta'
            ));
            return $response;
        }

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("default_image", $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
            ));
            return $response;
        }

        if(!$this->get("user_extension")->belongsFilter($album))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Nustatyti titulines nuotraukas galima tik savo albumams.'
            ));
            return $response;
        }

        $album->setDefaultImage($image);
        $this->getDoctrine()->getManager()->flush();
        $response->setData(array(
            "success" => true,
            "message" => 'Titulinė nuotrauka pakeista.'
        ));
        return $response;
    }
}