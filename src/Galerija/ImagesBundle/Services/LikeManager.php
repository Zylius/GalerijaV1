<?php
namespace Galerija\ImagesBundle\Services;
use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Like;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class Like Service'as
 * @package Galerija\ImagesBundle\Services
 */
class LikeManager
{
    protected $em;
    protected $formFactory;
    protected $router;
    protected $im;

    /**
     * @param EntityManager $em
     * @param FormFactoryInterface $formFactory
     * @param Router $router
     * @param ImageManager $im
     */
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, Router $router, ImageManager $im)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->im = $im;
    }

    /**
     * Išsaugomas Like'as duom. bazėj
     *
     * Užklausos:
     * INSERT INTO likes (created, updated, imageId, userId) VALUES (?, ?, ?, ?) {...}
     * UPDATE images SET like_count = like_count + 1, updated = ? WHERE imageId = ?
     *
     * @param Like $like Like'as kurį išsaugosim
     */
    public function save(Like $like)
    {
        $this->em->persist($like);
        $this->em->flush();
    }

    /**
     * Ištrinamas Like'as duom. bazėj
     *
     * Užklausos:
     * UPDATE images SET like_count = like_count - 1, updated = ? WHERE imageId = ?
     * DELETE FROM likes WHERE likeId = ?
     *
     * @param Like $like Like'as kurį ištrinsim
     */
    public function remove(Like $like)
    {
        $this->em->remove($like);
        $this->em->flush();
    }

    /**
     * Apkeičiamas Like'o statusas pagal tai ar šis vartotojas jį jau palikin'o ar ne
     *
     * Užklausą galima rasti \Entity\LikeRepository
     *
     * @param Image $image
     * @param User $user
     */
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
            $this->save($like);
        }
    }
}