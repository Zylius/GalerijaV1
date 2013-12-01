<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;
/**
 * Albumų custom užklausų repozitorija
 */
class AlbumRepository extends EntityRepository
{
    /**
     * Suranda visus albumus kurie turėtų būti automatiškai pažymėti
     *
     * @param int $id albumo iš kurio atėjom id
     * @return Array rasti albumai
     */
    public function findAutoSelect($id)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT p FROM GalerijaImagesBundle:Album p WHERE p.auto_add = TRUE OR p.albumId = :id'
        )->setParameter('id', $id);
        $results = $query->getResult();
        return $results;
    }
}