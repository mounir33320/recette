<?php

namespace App\DataFixtures;



use App\Entity\Categorie;
use App\Repository\RecetteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{

    private $recetteRepository;

    public function __construct(RecetteRepository $recetteRepository)
    {
        $this->recetteRepository = $recetteRepository;
    }

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

        /*for($i=0; $i<=30; $i++){

            $categorie = new Categorie();
            $categorie->setNom("Catégorie {$i}");

            $manager->persist($categorie);
        }
        $manager->flush();*/
    }
}
