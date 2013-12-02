<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LikeRepository extends EntityRepository
{

    /**
     * Suranda like'ą pagal vartotoją ir nuotrauką
     * Užklausa:
     * SELECT l0_.likeId AS likeId0,
     * l0_.created AS created1,
     * l0_.updated AS updated2,
     * l0_.imageId AS imageId3,
     * l0_.userId AS userId4
     * FROM likes l0_
     * WHERE l0_.imageId = ? AND l0_.userId = ?
     *
     * @param  int $imageId nuotraukos id
     * @param int $userId vartotojo id
     * @return array
     */
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