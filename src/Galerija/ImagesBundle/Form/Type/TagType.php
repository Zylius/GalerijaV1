<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Tag tipo forma
 * @package Galerija\ImagesBundle\Form\Typez
 */
class TagType extends AbstractType
{
    /**
     * Sukurio tag formą
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'textarea', array('label' => "Pavadinimas"));
        $builder->add('Sukurti', 'submit', array('label' => "Sukurti"));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tag_create';
    }
}