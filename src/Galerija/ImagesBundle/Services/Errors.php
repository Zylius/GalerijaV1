<?php
namespace Galerija\ImagesBundle\Services;
use Symfony\Component\Validator\Validator;

class Errors
{
    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }
   public function getErrors($object)
   {
       $errors = $this->validator->validate($object);
       $result = "";
       foreach( $errors as $error )
       {
           $result .= $error->getMessage();
       }
       return $result;
   }
}