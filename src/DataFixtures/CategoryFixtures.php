<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName("Asiatique");
        $manager->persist($category);
        $manager->flush();
    }
}
