<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
}
