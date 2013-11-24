<?php

namespace Galerija\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
class TagController extends Controller
{
    public function submitAction(Request $request)
    {
        $tag = new Tag();

        $tm = $this->get("tag_manager");

        $form = $tm->getForm($tag);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tm->save($tag);
            $this->get('session')->getFlashBag()->add('success', 'Tag\'as sukurtas sėkmingai!');
            return $this->redirect($request->headers->get('referer'));
        }
    }
}