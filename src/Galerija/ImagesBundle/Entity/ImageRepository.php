<?php
namespace Galerija\ImagesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use \Galerija\ImagesBundle\Resources\config\Constants;
/**
 * Albumų custom užklausų repozitorija
 */
class ImageRepository extends EntityRepository
{
    /**
     * Iš anksto užkraunami tag'ai pagal paveikslėlių masyvą (nebenaudojama)
     *
     * @deprecated
     * @param Array $image_ids paveikslėlių id masyvas
     * @return Array
     */
    public function preloadTags(Array $image_ids)
    {
        $query =  $this->getEntityManager()->createQuery(
            'SELECT i, t FROM GalerijaImagesBundle:Image i
             LEFT JOIN i.tags t WHERE i.imageId IN (:ids)'
        )->setParameter('ids', $image_ids);
        $results = $query->getResult();
        return $results;
    }

    /**
     * Gaunami paveiksliukai pagal albumo id ir dabartinį puslapį
     *
     * Užklausa:
     *      SELECT
     *           i0_.imageId AS imageId0,
     *           i0_.created AS created1,
     *           i0_.updated AS updated2,
     *           i0_.pavadinimas AS pavadinimas3,
     *           i0_.aprasymas AS aprasymas4,
     *           i0_.like_count AS like_count5,
     *           i0_.ext AS ext6,
     *           i0_.shot_date AS shot_date7,
     *           t1_.tagId AS tagId8,
     *           t1_.name AS name9,
     *           i0_.userId AS userId10,
     *           t1_.userId AS userId11
     *       FROM
     *           images i0_
     *           LEFT JOIN albums_images a3_ ON i0_.imageId = a3_.imageId
     *           LEFT JOIN albums a2_ ON a2_.albumId = a3_.albumId
     *           LEFT JOIN tags_images t4_ ON i0_.imageId = t4_.imageId
     *           LEFT JOIN tags t1_ ON t1_.tagId = t4_.tagId
     *       WHERE
     *           a2_.albumId = ?
     *       ORDER BY
     *           i0_.imageId DESC
     *       LIMIT
     *           5 OFFSET 0
     *
     * @param int $id albumo Id
     * @param int $page puslapio numeris
     * @return ArrayCollection rezultatai
     */
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

    /**
     * Gaunami paveiksliukai pagal vartotoją ir dabartinį puslapį
     *
     * Užklausa:
     *   SELECT
     *       i0_.imageId AS imageId0,
     *       i0_.created AS created1,
     *       i0_.updated AS updated2,
     *       i0_.pavadinimas AS pavadinimas3,
     *       i0_.aprasymas AS aprasymas4,
     *       i0_.like_count AS like_count5,
     *       i0_.ext AS ext6,
     *       i0_.shot_date AS shot_date7,
     *       t1_.tagId AS tagId8,
     *       t1_.name AS name9,
     *       i0_.userId AS userId10,
     *       t1_.userId AS userId11
     *   FROM
     *       images i0_
     *       LEFT JOIN tags_images t2_ ON i0_.imageId = t2_.imageId
     *       LEFT JOIN tags t1_ ON t1_.tagId = t2_.tagId
     *       LEFT JOIN user u3_ ON i0_.userId = u3_.id
     *   WHERE
     *       u3_.id = ?
     *   ORDER BY
     *       i0_.imageId DESC
     *   LIMIT
     *       5 OFFSET 0
     *
     * @param int $id vartotojo id
     * @param int $page puslapio numeris
     * @return ArrayCollection rezultatai
     */
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