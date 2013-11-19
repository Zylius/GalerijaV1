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
        $builder->add('image', 'hidden', array('mapped' => false));
    }

    public function getName()
    {
        return 'comment_post';
    }
    public function getDefaultOptions()
    {
        return array('data_class' => 'Galerija\ImagesBundle\Entity\Image');
    }
}