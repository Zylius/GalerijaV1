<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\ImageType;
use Galerija\ImagesBundle\Entity\Album;
use Galerija\ImagesBundle\Form\Type\AlbumType;
use Symfony\Component\HttpFoundation\Request;
use Galerija\ImagesBundle\Form\RegistrationFormType;

/**
 * Class Profilio kontroleris
 *
 * @package Galerija\ImagesBundle\Controller
 */
class ProfileController extends Controller
{

    /**
     * Generuojama userio panelė (registracijos, prisijungimo forma) ir atvaizduojama,
     * vėliau controlleris renderinamas template index.html.twig
     *
     * @return mixed
     */
    public function fullPanelAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->get('fos_user.registration.form.factory')->createForm();
        $token = $this->get('form.csrf_provider')->generateCsrfToken('authenticate');

        return $this->render('GalerijaImagesBundle:User:profile_control.html.twig', array(
            'regform' => $form->createView(),
            'token' => $token,
            'user' => $user,
        ));
    }

}
