<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findCommentsByImage($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Comment p WHERE p.image = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
}