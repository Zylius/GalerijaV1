<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class ImageType extends AbstractType
{
    const MAX_SIZE = 4194304; //4MB
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('pavadinimas', 'text',  array('required' => false,
            'attr' =>
            array
            (
                'size' =>'40',
                'placeholder' => 'Neprivalomas'
            )
        ));
        $builder->add('aprasymas', 'text', array('label' => "Aprašymas",
            'attr' =>
            array
            (
                'size' =>'40',
                'placeholder' => 'Privalomas'
            )
        ));
        $builder->add('failas', 'file', array(
            'attr' =>
            array
            (
                'accept' => 'image/*',
                'size' =>'40'
            )
        ));
        $builder->add('shot_date', 'date', array(
            'input'  => 'datetime',
            'widget' => 'choice',
        ));
        $builder->add('Ikelti', 'submit', array('label' => "Įkelti"));

    }

    public function getName()
    {
        return 'image_upload';
    }
}