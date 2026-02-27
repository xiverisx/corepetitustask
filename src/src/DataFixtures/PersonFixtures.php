<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonFixtures extends Fixture
{
    public const string PERSON_REFERENCE = 'person_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $person = new Person();
            $person->firstName = $faker->firstName();
            $person->surName = $faker->lastName();

            $manager->persist($person);

            $this->addReference(self::PERSON_REFERENCE.$i, $person);
        }

        $manager->flush();
    }
}
