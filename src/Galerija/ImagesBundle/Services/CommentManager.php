<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class CommentManager
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
    public function getForm(Comment $comment, Image $image = null)
    {
        $form = $this->formFactory->create(new CommentType(), $comment, array(
            'action' => $this->router->generate('galerija_images_comment')
        ));
        if($image)
            $form->get('image')->setData($image->getImageId());
        return $form;
    }
    public function save(Comment $comment)
    {
        $this->em->persist($comment);
        $this->em->flush();
    }
    public function remove(Comment $comment)
    {
        $this->em->remove($comment);
        $this->em->flush();
    }
    public function findByImage(Image $image)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Comment')->findCommentsByImage($image->getImageId());
        return $value;
    }
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Comment')->find($id);
        return $value;
    }
}