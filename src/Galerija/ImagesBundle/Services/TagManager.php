<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class TagManager
{
    protected $em;
    protected $formFactory;
    protected $router;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }
    public function getForm($tag)
    {
        return $this->formFactory->create(new TagType(), $tag, array(
            'action' => $this->router->generate('galerija_tag_create'),
        ));
    }
    public function save($tag)
    {
        $this->em->persist($tag);
        $this->em->flush();
    }
    public function formatTags(Image $image)
    {
        if($image->getTags() == null)
            return "";

        $tags = "";
        foreach ($image->getTags()->toArray() as $arr) {
            $tags .= ' tag-'.$arr->getName();
        }
        return $tags;
    }
    public function preloadTags($images)
    {
        $image_ids = Array();
        foreach($images as $image)
        {
            array_push($image_ids, $image->getImageId());
        }
        $this->em->getRepository('GalerijaImagesBundle:Tag')->findByImages($image_ids);
    }
}