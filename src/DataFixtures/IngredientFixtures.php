<?php

namespace App\DataFixtures;



use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{

//    private $ingredientRepository;
//
//    public function __construct(IngredientRepository $ingredientRepository)
//    {
//        $this->ingredientRepository = $ingredientRepository;
//    }

    public function load(ObjectManager $manager)
    {
//       $categorie = new Categorie();
//       $recette = $this->recetteRepository->find(1);
//
//       $categorie->setNom("Oriental")
//                 ->addRecette($recette);
//
//        $manager->persist($categorie);
//        $manager->flush();

        for($i=0; $i<=30; $i++){

            $ingredient = new Ingredient();
            $ingredient->setNom("Ingredient {$i}");

            $manager->persist($ingredient);
        }
        $manager->flush();
    }
}
