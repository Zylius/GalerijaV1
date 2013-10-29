<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AlbumRepository extends EntityRepository
{
    public function findOne()
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT p, c FROM GalerijaImagesBundle:Image p
            JOIN p.category c
            WHERE p.id = :id'
            )->setParameter('id', $this->albumId);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}