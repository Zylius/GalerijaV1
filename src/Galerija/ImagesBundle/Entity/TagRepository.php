<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    public function findByImages($image_ids)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT i, t FROM GalerijaImagesBundle:Image i
             JOIN i.tags t WHERE i.imageId IN (:ids)'
        )->setParameter('ids', $image_ids);
        $results = $query->getResult();
        return $results;
    }
}