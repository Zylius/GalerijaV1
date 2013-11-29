<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AlbumRepository extends EntityRepository
{
    public function findAutoSelect($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Album p WHERE p.auto_add = TRUE OR p.albumId = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
    public function loadImages($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p, i, t FROM GalerijaImagesBundle:Album p
             LEFT JOIN p.images i
             LEFT JOIN i.tags t
             WHERE p.albumId = :id'
        )->setParameter('id', $id);
        $results = $query->getSingleResult();
        return $results;
    }

}