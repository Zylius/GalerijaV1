<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Album;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Albumų sąrašo kontroleris (pagrindinis puslapis)
 * @package Galerija\ImagesBundle\Controller
 */
class AlbumListController extends Controller
{
    /**
     * Atvaizduojamas pagrindinis pulsapis, ir jei pareikalauta (post metodas) sukuriamas naujas albumas.
     * Kurimo atveju, tikrinama, ar vartotojas prisijungęs.
     *
     * @param Request $request užklausa
     * @return mixed suformuotas puslapis
     */
    public function indexAction(Request $request)
    {
        $album = new Album();
        /* @var \Galerija\ImagesBundle\Services\AlbumManager $am */
        $am = $this->get("album_manager");
        $form = $am->getForm($album);
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($request->isMethod('POST'))
        {
            $securityContext = $this->container->get('security.context');
            if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
            {
                $this->get('session')->getFlashBag()->add('error', 'Kurti albumus gali tik prisijungę vartotojai.');
                return $this->redirect($this->generateUrl('galerija_album_homepage'));
            }
            $form->handleRequest($request);
            if ($form->isValid()) {
                $album->setUser($user);
                $am->save($album);
                $this->get('session')->getFlashBag()->add('success', 'Albumas sukurtas sėkmingai!');
                return $this->redirect($this->generateUrl('galerija_album_homepage'));
            }
            else
            {
                $result = $this->get("errors")->getErrors($album);
                $this->get('session')->getFlashBag()->add('error',$result);
                return $this->redirect($this->generateUrl('galerija_album_homepage'));
            }
        }

        $album_array = $am->findAll();
        return $this->render('GalerijaImagesBundle:Albums:albums.html.twig', array(
            'album_array' => $album_array,
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * Albumų trinimmo veiksmas.
     * Kadangi šiuo atveju nesinaudojama Symfony formomis, patikrinamas gautas csf token'as.
     * Patikrinama ar albumas priklauso vartotojui, ar pats albumas rastas.
     *
     * @param Request $request užklausa, id nurodo trinamo albumo id
     * @return JsonResponse atsakas, kuris apdorojamas DeleteWidget.js faile
     */
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();
        /* @var \Galerija\ImagesBundle\Services\AlbumManager $am */
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

    /**
     * Titulinės nuotraukos nustatymo veiksmas.
     * Patikrinama ar vartotojas prisijungęs ir ar jam priklauso nurodytas albumas.
     * Taip pat tikrinama ar paveiksliukas ir albumas egzistuoja.
     *
     * @param Request $request užklausa. aID nurodo albumo iD, ID nurodo nuotraukos I
     * @return JsonResponse atsakas, kuri apdorojamas DefaultImageWidget.js faile
     */
    public function setDefaultAction(Request $request)
    {
        $response = new JsonResponse();
        $aid = (int)$request->request->get('aID');
        $id = (int)$request->request->get('ID');
        /* @var Album $album */
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

        if($image == null)
        {
            $response->setData(array(
                    "success" => false,
                    "message" => 'Tokio paveiksliuko nerasta'
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

    /**
     * Albumo redagavimas. Patikrinama ar albumas vartotojui priklauso,
     * ar ji egzistuoja ir sugeneruojama forma vartotojui.
     * Jei metodas yra post, vadinasi submitinamas redagavimas, dėl ko patikrinama forma ir gražinamas rezultatas
     *
     * @param Request $request užklausa
     * @param int $albumId albumo id
     * @return mixed rezuktatas
     */
    public function editAction(Request $request, $albumId)
    {
        /* @var \Galerija\ImagesBundle\Services\AlbumManager $am */
        $am = $this->get("album_manager");
        $album = $am->findById($albumId);
        $form = $am->getEditForm($album);

        if($album == null || $album->getAlbumId() == 0)
        {
            $this->get('session')->getFlashBag()->add('error', 'Tokio albumo nerasta.');
            return $this->redirect($this->generateUrl('galerija_album_homepage'));
        }

        if(!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $this->get('session')->getFlashBag()->add('success', 'Redaguoti albumus gali tik prisijungę vartotojai.');
            return $this->redirect($request->headers->get('referer'));
        }

        if(!$this->get("user_extension")->belongsFilter($album))
        {
            $this->get('session')->getFlashBag()->add('error', 'Šis albumas nepriklauso jums');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($request->isMethod('POST'))
        {

            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'Albumas sėkmingai atnaujintas.');
                return $this->redirect($request->headers->get('referer'));
            }
            else
            {
                $result = $this->get("errors")->getErrors($album);
                $this->get('session')->getFlashBag()->add('error',$result);
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('GalerijaImagesBundle:Forms:album.html.twig', array(
            'form' => $form->createView(),
            'edit' => true
        ));
    }
}