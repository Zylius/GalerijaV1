<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function findUserImages($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Image p WHERE p.user = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
}