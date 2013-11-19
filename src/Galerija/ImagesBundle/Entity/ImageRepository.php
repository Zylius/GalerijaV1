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
}