<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use \Galerija\ImagesBundle\Resources\config\Constants;
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

    public function findForPageByAlbum($id, $page)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT i, t FROM GalerijaImagesBundle:Image i
             LEFT JOIN i.albums a
             LEFT JOIN i.tags t
             WHERE a.albumId = :id ORDER BY i.imageId DESC'
        )->setFirstResult(($page - 1) * Constants::IMAGES_PER_PAGE )->setMaxResults(Constants::IMAGES_PER_PAGE)->setParameter('id', $id);
        $results = $query->getResult();
        return new ArrayCollection($results);
    }
    public function findForPageByUser($id, $page)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT i, t FROM GalerijaImagesBundle:Image i
             LEFT JOIN i.tags t
             LEFT JOIN i.user u
             WHERE u.id = :id ORDER BY i.imageId DESC'
        )->setFirstResult(($page - 1) * Constants::IMAGES_PER_PAGE )->setMaxResults(Constants::IMAGES_PER_PAGE)->setParameter('id', $id);
        $results = $query->getResult();
        return new ArrayCollection($results);
    }
}