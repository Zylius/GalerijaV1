<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'textarea', array('label' => "Pavadinimas"));
        $builder->add('Sukurti', 'submit', array('label' => "Sukurti"));
    }

    public function getName()
    {
        return 'tag_create';
    }
}