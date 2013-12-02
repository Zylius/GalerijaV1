<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Paveksliukų kontroleris
 *
 * @package Galerija\ImagesBundle\Controller
 */
class ImageController extends Controller
{
    /**
     * Atvaizduoja paveiksliuko visa informaciją. (fancybox). Patikrinama, ar vartotojas prisijungęs.
     * Jei taip, sužinoma ar jam "patinka" ši nuotrauka
     *
     * @param int$imageId nuotraukos id
     * @return mixed sugeneruotas puslapis
     */
    public function  showInfoAction($imageId)
    {
        $securityContext = $this->container->get('security.context');
        $liked = false;

        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $userId = $securityContext->getToken()->getUser()->getId();
            $liked = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Like')->findLikesByImageUser($imageId, $userId);
        }

        $image = $this->get("image_manager")->findById($imageId);
        $comment = new Comment();
        $comment->setImage($image);
        $comment_array = $this->get("comment_manager")->findByImage($image);

        return $this->render('GalerijaImagesBundle:Images:image_info.html.twig', array(
            'image' => $image,
            'form' => $this->get("comment_manager")->GetForm($comment,$image)->createView(),
            'comments' => $comment_array,
            'liked' => $liked
        ));
    }

    /**
     * Jei viskas teisingai (vartotojas autentifikuotas, forma teisinga ir t.t.) įkelia paveiksliuką
     * ir grąžina statusą
     *
     * @param Request $request užklausa
     * @param int $albumId albumoId
     * @return JsonResponse grąžinama informacija, kuri atvaizduojama UploadWidget.js pagalba
     */
    public function uploadAction(Request $request, $albumId)
    {
        $securityContext = $this->container->get('security.context');
        $response = new JsonResponse();

        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Įkelti nuotraukas gali tik prisijungę vartotojai.'
            ));
            return $response;
        }

        /* @var \Galerija\ImagesBundle\Services\ImageManager $im  */
        $im = $this->get("image_manager");
        $image = new Image();
        $form = $im->getForm($image);
        $form->handleRequest($request);
        $image->setUser($this->container->get('security.context')->getToken()->getUser());

        if($form->isValid())
        {

            $im->uploadProcedures($image);

            //patikrinam ar albumas iš kurio buvo įkelta buvo įtrauktas į šios nuotraukos albumų sąrašą
            if($albumId == 0 || $this->get('album_manager')->findAlbumInArray($albumId, $image->getAlbums()->toArray()))
            {
                return $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                    "value" => $this->render('GalerijaImagesBundle:Images:single_image_thumb.html.twig', array(
                        'image' => $image,
                        'album' => $this->get('album_manager')->findById($albumId),
                    ))->getContent()
                ));
            }
            else
            {
                $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                ));
            }

            return $response;
        }

        $result = $this->get("errors")->getErrors($image);
        //kai php.ini failo dydis viršijamas, gražinamas tuščias klaidų sąrašas
        if($result == "")
            $result = "Failas per didelis";

        $response->setData(array(
            "success" => false,
            "message" => $result
        ));

        return $response;
    }

    /**
     * Trinama nuotrauka. Kadangi šiuo atveju nesinaudojama Symfony formomis, patikrinamas gautas csf token'as.
     * $aid nurodo iš kurio albumo trinama, jei jo reikšmė nulis, trinama iš visų albumų.
     * $id nurodo kuri nuotrauka trinama.
     * Patikrinama ar toks paveiksliukas yra, ar vartotojas prisijungęs ir ar ja mperiklauso paveiksliukas
     *
     * @param Request $request užklausa
     * @return JsonResponse rezultatas, kuris apdorojamas "DeleteImageWidgets.js" faile
     */
    public function deleteAction(Request $request)
    {
        $response = new JsonResponse();

        /* @var \Galerija\ImagesBundle\Services\ImageManager $im  */
        $im = $this->get("image_manager");
        $id = (int)$request->request->get('ID');
        $aid = (int)$request->request->get('aID');
        $image = $im->findById($id);

        if($image == null)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio paveiksliuko nerasta'
            ));
            return $response;
        }

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("image".$id, $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
            ));
            return $response;
        }

        if(!$this->get("user_extension")->belongsFilter($image))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Galima trinti tik savo nuotraukas.'
            ));
            return $response;
        }

        $im->delete($image,  $this->get("album_manager")->findById($aid));
        $response->setData(array(
            "success" => true,
            "message" => 'Failas ištrintas sėkmingai!'
        ));

        return $response;

    }

    /**
     * Nuotraukos redagavimas. Patikrinama ar nuotrauka vartotojui priklauso,
     * ar ji egzistuoja ir sugeneruojama forma vartotojui.
     * Jei metodas yra post, vadinasi submitinamas redagavimas, vadinasi patikrinama forma ir gražinamas rezultatas
     *
     * @param Request $request užklausa
     * @param int $imageId nuotraukos Id
     * @return mixed sugeneruotas puslapis
     */
    public function editAction(Request $request, $imageId)
    {
        /* @var \Galerija\ImagesBundle\Services\ImageManager $im  */
        $im = $this->get("image_manager");
        $image = $im->findById($imageId);
        $form = $im->getEditForm($image);

        if($image == null || $image->getImageId() == 0)
        {
            $this->get('session')->getFlashBag()->add('error', 'Tokio paveiksliuko nerasta.');
            return $this->redirect($this->generateUrl('galerija_album_homepage'));
        }

        if(!$this->get("user_extension")->belongsFilter($image))
        {
            $this->get('session')->getFlashBag()->add('error', 'Šis paveiksliukas nepriklauso jums');
            return $this->redirect($request->headers->get('referer'));
        }

        if(!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $this->get('session')->getFlashBag()->add('success', 'Redaguoti nuotraukas gali tik prisijungę vartotojai.');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->get("album_manager")->recalculateCount($image);
                $this->get('session')->getFlashBag()->add('success', 'Nuotrauka sėkmingai atnaujinta.');
                return $this->redirect($request->headers->get('referer'));
            }
            else
            {
                $result = $this->get("errors")->getErrors($image);
                $this->get('session')->getFlashBag()->add('error',$result);
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('GalerijaImagesBundle:Forms:image_upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}