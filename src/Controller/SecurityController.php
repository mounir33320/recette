<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods="POST")
     * @param AuthenticationUtils $authenticationUtils
     * @return JsonResponse
     */
    public function logIn(AuthenticationUtils $authenticationUtils):JsonResponse
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $loggedUser = $this->getUser();
        return new JsonResponse([
            "username" => $loggedUser->getUsername(),
            "roles" => $loggedUser->getRoles()
            ]);
    }
}