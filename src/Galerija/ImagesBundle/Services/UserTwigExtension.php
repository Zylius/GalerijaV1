<?php
namespace Galerija\ImagesBundle\Services;
use Symfony\Component\Security\Core\SecurityContext;

class UserTwigExtension extends \Twig_Extension
{
    protected $sc;

    public function __construct(SecurityContext $sc)
    {
        $this->sc = $sc;
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('belongs', array($this, 'belongsFilter')),
        );
    }
    public function belongsFilter($object)
    {
        if(!method_exists ( $object , "getUser" ))
            return false;
        if($this->sc->isGranted('ROLE_ADMIN') === true || $object->getUser() == $this->sc->getToken()->getUser())
            return true;
        return false;
    }
    public function getName()
    {
        return 'filter_extension';
    }
}