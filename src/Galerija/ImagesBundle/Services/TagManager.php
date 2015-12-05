<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\TagType;
use Galerija\ImagesBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class Tag Service'as
 * @package Galerija\ImagesBundle\Services
 */
class TagManager
{
    protected $em;
    protected $formFactory;
    protected $router;

    /**
     * @param EntityManager $em
     * @param FormFactoryInterface $formFactory
     * @param Router $router
     */
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * Padaroma forma tago sukūrimui
     *
     * @param $tag Tag'as pagal kurį kuriama forma
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(Tag $tag)
    {
        return $this->formFactory->create(new TagType(), $tag, array(
            'action' => $this->router->generate('galerija_tag_create'),
        ));
    }

    /**
     * Išsaugomas tagas
     * Užklausa:
     *  INSERT INTO tags (name, userId) VALUES (?, ?) {"", ""}
     * @param $tag Tag'as kurį išsaugome
     */
    public function save(Tag $tag)
    {
        $this->em->persist($tag);
        $this->em->flush();
    }

    /**
     * Suformuojamas tagų stringas paveiksliukui, pagal kurį po to filtruojama
     *
     * @param Image $image paveiksliukas kuriam formuojamas tag'ų stringas
     * @return string
     */
    public function formatTags(Image $image)
    {
        if($image->getTags() == null)
            return "";

        $tags = "";
        /* @var $arr Tag */
        foreach ($image->getTags() as $arr) {
            $tags .= ' tag-'.$arr->getName();
        }
        return $tags;
    }

    /**
     * Suformuojamas visų tagų stringas autocompletion jQuery pluginui
     *
     * @return string
     */
    public function formatAllTags()
    {
        $tags = "";
        /* @var $arr Tag */
        foreach ($this->em->getRepository('GalerijaImagesBundle:Tag')->findAll() as $arr) {
            $tags .= $arr->getName().',';
        }
        $tags = rtrim($tags, ',');
        return $tags;
    }

}