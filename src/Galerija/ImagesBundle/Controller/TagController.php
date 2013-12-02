<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Tag'ų kontroleris
 *
 * @package Galerija\ImagesBundle\Controller
 */
class TagController extends Controller
{
    /**
     * Tagų sukūrimo kontroleris, patikrinama ar vartotojas autentifikuotas, ir validuojama forma,
     * jei viskas teisinga išsaugoma
     *
     * @param Request $request užklausa
     * @return mixed
     */
    public function submitAction(Request $request)
    {
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $this->get('session')->getFlashBag()->add('error', 'Tag\'us kurti gali tik prisijungę vartotojai.');
            return $this->redirect($request->headers->get('referer'));
        }
        $tag = new Tag();

        /* @var $tm \Galerija\ImagesBundle\Services\TagManager*/
        $tm = $this->get("tag_manager");

        $form = $tm->getForm($tag);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag->setUser($this->get('security.context')->getToken()->getUser());
            $tm->save($tag);
            $this->get('session')->getFlashBag()->add('success', 'Tag\'as sukurtas sėkmingai!');
            return $this->redirect($request->headers->get('referer'));
        }
        else
        {
            $result = $this->get("errors")->getErrors($tag);
            $this->get('session')->getFlashBag()->add('error', $result);
            return $this->redirect($request->headers->get('referer'));
        }

    }
}