<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pavadinimas', 'file');
        $builder->add('save', 'submit');

    }

    public function getName()
    {
        return 'image_upload';
    }
}