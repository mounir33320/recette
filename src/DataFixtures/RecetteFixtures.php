<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecetteFixtures extends Fixture
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    /**
     * @var RecetteRepository
     */
    private $recetteRepository;

    public function __construct(UserRepository $userRepository, CategorieRepository $categorieRepository, RecetteRepository $recetteRepository)
    {
        $this->userRepository = $userRepository;
        $this->categorieRepository = $categorieRepository;
        $this->recetteRepository = $recetteRepository;
    }

    public function load(ObjectManager $manager)
    {
        $categories = $this->categorieRepository->findAll();
        $recettes = $this->recetteRepository->findAll();
        $k = 0;
        $i = 1;
        $j = 2;

        foreach($recettes as $recette){
            if($i <= 23){
                $recette->addCategory($categories[$i])
                    ->addCategory($categories[$i])
                    ->addCategory($categories[$j]);
                $k++;
                $i++;
                $j++;
                $manager->persist($recette);
                $manager->flush();
            }

        }



    }
}
