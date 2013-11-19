<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', 'textarea', array('label' => "Komentaras"));
        $builder->add('Ikelti', 'submit', array('label' => "Įkelti"));
    }

    public function getName()
    {
        return 'album_create';
    }
}