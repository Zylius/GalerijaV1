<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Tag;
use Galerija\ImagesBundle\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class TagManager
{
    protected $em;
    protected $formFactory;
    protected $tag;
    protected $router;
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $tag = new Tag();
    }
    public function set($tag)
    {
        $this->tag  = $tag;
    }
    public function get()
    {
        return $this->tag;
    }
    public function getForm()
    {
        return $this->formFactory->create(new TagType(), $this->tag, array(
            'action' => $this->router->generate('galerija_tag_create'),
        ));
    }
    public function save()
    {
        $this->em->persist($this->tag);
        $this->em->flush();
    }
}