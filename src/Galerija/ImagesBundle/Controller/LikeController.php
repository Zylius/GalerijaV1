<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Like'ų controlleris
 * @package Galerija\ImagesBundle\Controller
 */
class LikeController extends Controller
{
    /**
     * "apkeitimo" metodas, jei vartotojui paveiksliukas jau patinka, įvykdžius šį metodą jis nebepatiks,
     * ir atvirkščiai. Patikrinama ar vartotojas prisijungęs.
     * $ID kuriam paveiksliukui priskiriamas naujas like'as
     * @param Request $request užklausa
     * @return JsonResponse json tipo atsakymas, kuris apdorojamas LikeWidget.js javascripte
     */
    public function  submitAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $response = new JsonResponse();
        $ID = (int)$request->request->get('ID');
        /* @var \Galerija\ImagesBundle\Entity\Image $image */
        $image = $this->get("image_manager")->findById($ID);

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid("like".$ID, $request->request->get('csrf_token')))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Neteisingas CSRF.'
            ));
            return $response;
        }

        if($image == null)
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Tokio paveiksliuko nerasta.'
            ));
            return $response;
        }

        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                "success" => false,
                "message" => '"Like\'inti" gali tik prisijungę vartotojai.'
            ));
            return $response;
        }

        $this->get("like_manager")->toggle($image, $securityContext->getToken()->getUser());

        $response->setData(array(
            "success" => true,
            "count" => $image->getLikeCount()
        ));

        return $response;
    }
}