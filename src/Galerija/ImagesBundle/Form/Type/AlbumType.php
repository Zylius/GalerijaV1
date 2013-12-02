<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Albumo formos klasė
 *
 * @package Galerija\ImagesBundle\Form\Type
 */
class AlbumType extends AbstractType
{
    /**
     * Sukuriama forma
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('short_comment', 'text', array('label' => "Pavadinimas",
            'attr' =>
            array
            (
                'placeholder' => 'Privalomas'
            )
        ));
        $builder->add('long_comment', 'textarea', array('label' => "Aprašymas", 'required' => false));
        $builder->add('auto_add', 'checkbox', array('label' => "Automatiškai pažymėti", 'required' => false));
        $builder->add('Ikelti', 'submit', array('label' => "Patvirtinti"));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'album_create';
    }
}