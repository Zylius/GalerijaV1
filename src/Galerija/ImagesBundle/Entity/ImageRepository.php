<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function findAutoSelect($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Album p WHERE p.auto_add = TRUE OR p.albumId = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
    public function findLikedByImageUser($imageId, $userId)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p
             FROM GalerijaImagesBundle:Like p
             WHERE p.image = :image AND p.user = :user'
        )->setParameters(array(
                'image' => $imageId,
                'user' => $userId
            ));
        $results = $query->getResult();
        return $results;
    }
}