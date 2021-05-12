<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Annotations as OA;


class UserController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // php 8 #[Route('/user', name: 'user')]
    /**
     * @OA\Get(
     *     tags={"User"},
     *     path="/users/deactivate/{id}",
     *     summary="Deactivate a User",
     *     description="Deactivate a User",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id de l'utilisateur",
     *          required=true
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Deactivate a User",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string",example="L'utilisateur a bien été désactivé.")
     *          )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="403",
     *          ref="#/components/responses/forbidden"
     *     ),
     * )
     *
     * @Route("/users/deactivate/{id}", name="deactivate_user", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */
    public function deactivate(User $user): JsonResponse
    {
      $user->setActif(false);
      $this->entityManager->flush();

      $message = ["message"=>"L'utilisateur {$user->getFirstname()} {$user->getLastname()} à l'id {$user->getId()} à bien été désactivé."];

      return new JsonResponse($message,Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     tags={"User"},
     *     path="/users/activate/{id}",
     *     summary="Activate a User",
     *     description="Activate a User",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id de l'utilisateur",
     *          required=true
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Activate a User",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string",example="L'utilisateur a bien été activé.")
     *          )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="403",
     *          ref="#/components/responses/forbidden"
     *     ),
     * )
     * @Route("/users/activate/{id}", name="activate_user", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */
    public function activate(User $user): JsonResponse
    {
        $user->setActif(true);
        $this->entityManager->flush();

        $message = ["message"=>"L'utilisateur {$user->getFirstname()} {$user->getLastname()} à l'id {$user->getId()} à bien été activé."];

        return new JsonResponse($message,Response::HTTP_OK);
    }
}
