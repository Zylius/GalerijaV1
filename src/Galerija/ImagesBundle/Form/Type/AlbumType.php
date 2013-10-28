<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('short_comment', 'text', array('label' => "Komentaras",
            'attr' =>
            array
            (
                'size' =>'40',
                'placeholder' => 'Privalomas'
            )
        ));
        $builder->add('long_comment', 'textarea', array('label' => "Ilgas aprašymas"));
        $builder->add('Ikelti', 'submit', array('label' => "Įkelti"));
    }

    public function getName()
    {
        return 'album_create';
    }
}