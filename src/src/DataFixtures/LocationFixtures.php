<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\LocationType;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $location = new Location();
            $location->name = $faker->address();

            $randomPersonIndex = rand(0, 9);
            $randomTypeIndex = rand(0, 3);

            $location->setPerson(
                $this->getReference(
                    PersonFixtures::PERSON_REFERENCE.$randomPersonIndex,
                    Person::class
                )
            );

            $location->setLocationType(
                $this->getReference(
                    LocationTypeFixtures::TYPE_REFERENCE.$randomTypeIndex,
                    LocationType::class
                )
            );

            $manager->persist($location);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
            LocationTypeFixtures::class,
        ];
    }
}
