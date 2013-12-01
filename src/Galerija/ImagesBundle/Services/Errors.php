<?php
namespace Galerija\ImagesBundle\Services;
use Symfony\Component\Validator\Validator;

/**
 * Class Errors, suranda klaidas pagal formą ir jas grąžina
 * @package Galerija\ImagesBundle\Services
 */
class Errors
{
    protected $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Suranda klaidas pagal formą ir jas grąžina
     *
     * @param $object
     * @return string
     */
    public function getErrors($object)
   {
       $errors = $this->validator->validate($object);
       $result = "";

       /* @var $error \Symfony\Component\Validator\ConstraintViolationInterface */
       foreach( $errors as $error )
       {
           $result .= $error->getMessage();
       }
       return $result;
   }
}