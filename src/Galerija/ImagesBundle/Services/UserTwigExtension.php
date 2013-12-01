<?php
namespace Galerija\ImagesBundle\Services;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class UserTwigExtension
 * @package Galerija\ImagesBundle\Services
 */
class UserTwigExtension extends \Twig_Extension
{
    protected $sc;

    /**
     * @param SecurityContext $sc
     */
    public function __construct(SecurityContext $sc)
    {
        $this->sc = $sc;
    }

    /**
     * @return array sukurti filtrai
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('belongs', array($this, 'belongsFilter')),
        );
    }

    /**
     * Patikrinama, ar objektas priklauso vartotojui, t.y. jei vartotojas administratorius,
     * arba vartotojas yra objekto kūrėjas
     *
     * @param $object
     * @return bool
     */
    public function belongsFilter($object)
    {
        if(!method_exists ( $object , "getUser" ))
            return false;
        if($this->sc->isGranted('ROLE_ADMIN') === true || $object->getUser() == $this->sc->getToken()->getUser())
            return true;
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'filter_extension';
    }
}