<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
class ImageRepository extends EntityRepository
{
    public function findUserImages($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Image p WHERE p.user = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return new ArrayCollection($results);
    }
    public function preloadTags(Array $image_ids)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT i, t FROM GalerijaImagesBundle:Image i
             LEFT JOIN i.tags t WHERE i.imageId IN (:ids)'
        )->setParameter('ids', $image_ids);
        $results = $query->getResult();
        return $results;
    }
}