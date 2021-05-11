<?php
namespace App\Traits;

use App\Entity\Recette;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorErrorTrait
{
    public function validate(ValidatorInterface $validator, $entity)
    {
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            foreach ($errors as $error){
                $allErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            $response = new JsonResponse($allErrors,Response::HTTP_BAD_REQUEST);
            return $response;
        }
        return null;
    }
}