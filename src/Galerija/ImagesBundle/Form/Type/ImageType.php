<?php
namespace Galerija\ImagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('pavadinimas', 'text',  array('required' => true,
            'attr' =>
            array
            (
                'size' =>'40',
                'placeholder' => 'Privalomas'
            )
        ));

        $builder->add('aprasymas', 'text', array('required' => false, 'label' => "Aprašymas",
            'attr' =>
            array
            (
                'size' =>'40',
                'placeholder' => 'Neprivalomas'
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
            'data' => new \DateTime(),
            'input'  => 'datetime',
            'widget' => 'choice',
            'years'  => range(date("Y"),date("Y")-100)
        ));

        $builder->add('albums','entity', array(
                        'label' => 'Albumai',
                        'class' => 'Galerija\ImagesBundle\Entity\Album',
                        'property' => 'short_comment',
                        'multiple' => true,
                        'expanded' => false,
                        'required' => true));

        $builder->add('tags','entity', array(
                        'label' => 'Tag\'ai',
                        'class' => 'Galerija\ImagesBundle\Entity\Tag',
                        'property' => 'name',
                        'multiple' => true,
                        'expanded' => false,
                        'required' => false,
            )

        );

        $builder->add('Ikelti', 'submit', array('label' => "Įkelti"));
    }

    public function getName()
    {
        return 'image_upload';
    }
}