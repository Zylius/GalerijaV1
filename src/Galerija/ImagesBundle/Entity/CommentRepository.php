<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class CommentRepository
 * @package Galerija\ImagesBundle\Entity
 */
class CommentRepository extends EntityRepository
{
    /**
     * Komentaras surandamas pagal paveiksliuką
     *
     * Užklausa:
     * SELECT c0_.commentId AS commentId0,
     * c0_.comment AS comment1,
     * c0_.approved AS approved2,
     * c0_.created AS created3,
     * c0_.updated AS updated4,
     * c0_.userId AS userId5,
     * c0_.imageId AS imageId6
     * FROM comments c0_ WHERE c0_.imageId = ?
     *
     * @param int $id paveiksliuko id
     * @return array
     */
    public function findCommentsByImage($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Comment p WHERE p.image = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
}