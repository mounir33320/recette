<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecutityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function logIn(AuthenticationUtils $authenticationUtils):JsonResponse
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return new JsonResponse(["toto" => "toto"]);
    }
}