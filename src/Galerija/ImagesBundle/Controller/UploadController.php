<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
class UploadController extends Controller
{

    public function indexAction(Request $request, $albumId)
    {
        //patikrinam ar vartotojas prisijungęs
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


            //patikrinam ar albumas iš kurio buvo įkelta buvo įtrauktas į šios nuotraukos albumų sąrašą
            if($this->FindAlbum($albumId, $image->getAlbums()->toArray()) ||$albumId == 0)
            {
                $assetManager = $this->get('templating.helper.assets');
                $cacheManager = $this->container->get('liip_imagine.cache.manager');
                $this->container->get('liip_imagine.controller')->filterAction($this->getRequest(),$image->getWebPath(),'my_thumb');
                $srcPath = $cacheManager->getBrowserPath($image->getWebPath(), 'my_thumb');

                $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                    "thumb_path" =>  $assetManager->getUrl($srcPath),
                    "path" =>  $this->generateUrl(('galerija_images_image_info'), array('imageId' => $image->getImageId())),
                    "delpath" =>  $assetManager->getUrl("bundles/GalerijaImages/images/delete.png"),
                    "name" =>  $image->getPavadinimas(),
                    "ID" => $image->getImageId()
                ));
            }
            else
            {
                $response->setData(array(
                    "success" => true,
                    "message" => 'Failas įkeltas sėkmingai!',
                ));
            }
            //nustatom pranešimą ir parodom klientui
            return $response;
        }


        //surandam klaidas
        $errors = $this->get('validator')->validate($image);
        $result = "";
        foreach( $errors as $error )
        {
            $result .= $error->getMessage();
        }
        //kai php.ini failo dydis viršijamas, gražinamas tuščias klaidų sąrašas
        if($result == "")
            $result = "Failas per didelis";
        //nustatom pranešimą ir parodom klientui
        $response->setData(array(
            "success" => false,
            "message" => $result
        ));
        return $response;
    }
    //TODO: perkelti logiką į service'us
    public function FindAlbum($id, $arr)
    {
        foreach($arr as $album)
        {
            $aid = $album->getAlbumId();
            if($aid == $id)
                return true;
        }
        return false;
    }
}
