<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Like;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
class LikeManager
{
    protected $em;
    protected $formFactory;
    protected $router;
    protected $im;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router, ImageManager $im)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->im = $im;
    }
    public function save(Like $like)
    {
        $this->em->persist($like);
        $this->em->flush();
    }
    public function remove(Like $like)
    {
        $this->em->remove($like);
        $this->em->flush();
    }
    public function toggle(Image $image, User $user)
    {
        $liked = $this->em->getRepository('GalerijaImagesBundle:Like')->findLikesByImageUser(
            $image->getImageId(),
            $user->getId());
        if($liked == true)
        {
            $image->setLikeCount($image->getLikeCount() - 1);
            $this->remove($liked[0]);
        }
        else
        {
            $like = new Like();
            $like->setImage($image);
            $like->setUser($user);
            $image->setLikeCount($image->getLikeCount() + 1);

            $this->im->save($image);
            $this->save($like);
        }
    }
}