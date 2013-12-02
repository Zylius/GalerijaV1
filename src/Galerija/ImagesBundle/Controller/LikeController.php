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
     *
     * @param $imageId kuriam paveiksliukui priskiriamas naujas like'as
     * @return JsonResponse json tipo atsakymas, kuris apdorojamas LikeWidget.js javascripte
     */
    public function  submitAction($imageId)
    {
        $securityContext = $this->container->get('security.context');
        $response = new JsonResponse();

        $image = $this->get("image_manager")->findById($imageId);
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