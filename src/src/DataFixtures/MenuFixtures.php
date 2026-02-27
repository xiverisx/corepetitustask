<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Menu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 5; $i++) {
            $root = new Menu();
            $root->name = ucfirst($faker->unique()->word());
            $root->sortOrder = $i;
            $manager->persist($root);

            for ($j = 1; $j <= 3; $j++) {
                $child = new Menu();
                $child->name = ucfirst($faker->words(2, true));
                $child->sortOrder = $j;
                $child->childOfId = $root;
                $manager->persist($child);

                for ($k = 1; $k <= 2; $k++) {
                    $grandChild = new Menu();
                    $grandChild->name = ucfirst($faker->words(3, true));
                    $grandChild->sortOrder = $k;
                    $grandChild->childOfId = $child;
                    $manager->persist($grandChild);
                }
            }
        }

        $manager->flush();
    }
}
