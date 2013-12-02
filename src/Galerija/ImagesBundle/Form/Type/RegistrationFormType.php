<?php

namespace Galerija\ImagesBundle\Form\RegistrationFormType;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

/**
 * Registracijos forma
 *
 * Class RegistrationFormType
 * @package Galerija\ImagesBundle\Form\RegistrationFormType
 */
class RegistrationFormType extends BaseType
{
    /**
     * Sukuriam registracijos forma
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('name');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'registration';
    }
}