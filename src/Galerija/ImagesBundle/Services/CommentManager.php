<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Comment;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class Komentarų service'as
 * @package Galerija\ImagesBundle\Services
 */
class CommentManager
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
     * Sukuriama forma komentaro sukūrimui
     *
     * @param Comment $comment komentaras, pagal kurį kuriama forma
     * @param Image $image paveiksliukas kuriam kuriama forma
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(Comment $comment, Image $image = null)
    {
        $form = $this->formFactory->create(new CommentType(), $comment, array(
            'action' => $this->router->generate('galerija_comment_submit')
        ));
        if($image)
            $form->get('image')->setData($image->getImageId());
        return $form;
    }

    /**
     * Išsaugomas komentaras
     * Užklausa:
     * INSERT INTO comments (comment, approved, created, updated, userId, imageId) VALUES (?, ?, ?, ?, ?, ?) {}
     *
     * @param Comment $comment komentaras kurį saugosim
     */
    public function save(Comment $comment)
    {
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * Ištrinamas komentaras
     * Užklausa:
     * DELETE FROM comments WHERE commentId = ?
     *
     * @param Comment $comment komentaras kurį trinsim
     */
    public function remove(Comment $comment)
    {
        $this->em->remove($comment);
        $this->em->flush();
    }

    /**
     * Komentaras surandamas pagal paveiksliuką
     * Užklausa:
     * SELECT c0_.commentId AS commentId0,
     * c0_.comment AS comment1,
     * c0_.approved AS approved2,
     * c0_.created AS created3,
     * c0_.updated AS updated4,
     * c0_.userId AS userId5,
     * c0_.imageId AS imageId6
     * FROM comments c0_ WHERE c0_.imageId = ?
     *
     * @param Image $image paveiksliukas pagal kurį ieškosim
     * @return Comment
     */
    public function findByImage(Image $image)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Comment')->findCommentsByImage($image->getImageId());
        return $value;
    }

    /**
     * Komentaras surandamas pagal ID
     * Užklausa:
     * SELECT c0_.commentId AS commentId0,
     * c0_.comment AS comment1,
     * c0_.approved AS approved2,
     * c0_.created AS created3,
     * c0_.updated AS updated4,
     * c0_.userId AS userId5,
     * c0_.imageId AS imageId6
     * FROM comments c0_
     * WHERE c0_.commentId = ?
     *
     * @param $id
     * @return object
     */
    public function findById($id)
    {
        $value = $this->em->getRepository('GalerijaImagesBundle:Comment')->find($id);
        return $value;
    }
}