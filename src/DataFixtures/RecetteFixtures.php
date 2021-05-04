<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecetteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $recette = new Recette();

        $recette->setNom("Blanquette")
                ->setTempsPreparation(30)
                ->setCout(15)
                ->setNbPersonne(4)
                ->setPublic(true);


        $recette2 = new Recette();

        $recette2->setNom("Tartiflette")
                ->setTempsPreparation(45)
                ->setCout(30)
                ->setNbPersonne(6)
                ->setPublic(false);


        $recette3 = new Recette();

        $recette3->setNom("Tajine")
                ->setTempsPreparation(120)
                ->setCout(60)
                ->setNbPersonne(10)
                ->setPublic(false);

        $manager->persist($recette);
        $manager->persist($recette2);
        $manager->persist($recette3);
        $manager->flush();
    }
}
