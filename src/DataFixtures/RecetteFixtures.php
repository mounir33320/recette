<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecetteFixtures extends Fixture
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->userRepository->find(1);
        $recette = new Recette();

        $recette->setNom("Gâteau au chocolat")
            ->setTempsPreparation(45)
            ->setCout(10)
            ->setNbPersonne(4)
            ->setPublic(true)
            ->setUser($user);

        $manager->persist($recette);

        $manager->flush();
    }
}
