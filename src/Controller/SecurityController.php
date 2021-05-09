<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use OpenApi\Annotations as OA;

class SecurityController extends AbstractController
{
    /**
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/login",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username", "password"},
     *              @OA\Property(property="username", type="string", example="john@doe.fr"),
     *              @OA\Property(property="password", type="string")
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Login into the API",
     *          @OA\JsonContent(
     *              @OA\Property(property="username", type="string", example="john@doe.fr"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *          )
     *     )
     * )
     *
     *
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

    /**
     * @OA\Get(
     *     tags={"Authentication"},
     *     path="/logout",
     *     @OA\Response(
     *          response="200",
     *          description="Disconnect from API"
     *     )
     * )
     *
     *
     * @Route("/logout", name="logout")
     */
    public function logout(){
        return new JsonResponse(["message" => "Vous êtes deconnecté"]);
    }
}
