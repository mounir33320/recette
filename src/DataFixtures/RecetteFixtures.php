<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecetteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<=50; $i++)
        {
            $recette = new Recette();

            $recette->setNom("Recette " . $i)
                ->setTempsPreparation(10 + $i+1)
                ->setCout(5 + $i)
                ->setNbPersonne(4)
                ->setPublic(true);

            $manager->persist($recette);
        }

        $manager->flush();
    }
}
