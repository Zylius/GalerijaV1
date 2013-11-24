<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LikeRepository extends EntityRepository
{
    public function findLikesByImageUser($imageId, $userId)
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