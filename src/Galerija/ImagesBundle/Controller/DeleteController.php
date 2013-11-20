<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
class DeleteController extends Controller
{
    public function indexAction(Request $request)
    {
        //patikrinam ar vartotojas prisijungęs
        $securityContext = $this->container->get('security.context');
        $response = new JsonResponse();
        if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $response->setData(array(
                "success" => false,
                "message" => 'Trinti nuotraukas gali tik prisijungę vartotojai.'
            ));
            return $response;
        }

        //paimam trinamos nuotrakos ID ir patikrinam ar tokia yra db
        $id = (int)$request->request->get('ID');
        if($image = $this->getDoctrine()->getRepository('GalerijaImagesBundle:Image')->find($id))
        {
            //patikrinam ar failas egzistuoja ir ištrinam
            $image->delete($this->getDoctrine()->getManager());

            //nustatom pranešimą ir grąžinam klientui
            $response->setData(array(
                "success" => true,
                "message" => 'Failas ištrintas sėkmingai!'
            ));
            return $response;
        }

        $response->setData(array(
            "success" => false,
            "message" => 'Failo ištrinti nepavyko'
        ));

        return $response;
    }
}