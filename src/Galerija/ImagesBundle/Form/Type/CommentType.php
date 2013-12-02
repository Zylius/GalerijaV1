<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Komentaro formos klasė
 * @package Galerija\ImagesBundle\Form\Type
 */
class CommentType extends AbstractType
{
    /**
     * Sukuriama forma
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', 'textarea', array('label' => "Komentaras"));
        $builder->add('Ikelti', 'submit', array('label' => "Įkelti"));
        $builder->add('image', 'hidden', array('mapped' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comment_post';
    }

    /**
     * Nustatomas tipas nuotraukos persiuntimui.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array('data_class' => 'Galerija\ImagesBundle\Entity\Image');
    }
}